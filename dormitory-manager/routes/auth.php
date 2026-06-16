<?php

$routes['home'] = function (PDO $db): void {
    if (Auth::check()) {
        redirectTo(Auth::dashboardRoute());
    }

    redirectTo('login');
};

$routes['login'] = function (PDO $db): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($username, $password)) {
            redirectTo(Auth::dashboardRoute());
        }

        render('auth/login', [
            'title' => 'Login',
            'error' => 'Sai tài khoản hoặc mật khẩu.',
            'success' => null
        ]);
        return;
    }

    render('auth/login', [
        'title' => 'Login',
        'error' => null,
        'success' => isset($_GET['registered'])
            ? 'Đăng ký thành công. Bạn có thể đăng nhập.'
            : null
    ]);
};

$routes['register'] = function (PDO $db): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $studentCode = trim($_POST['student_code'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $dob = $_POST['dob'] ?? null;
        $faculty = trim($_POST['faculty'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $errors = [];

        if ($username === '') {
            $errors[] = 'Username không được để trống.';
        }

        if (strlen($username) < 4) {
            $errors[] = 'Username phải có ít nhất 4 ký tự.';
        }

        if ($password === '') {
            $errors[] = 'Password không được để trống.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password phải có ít nhất 6 ký tự.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp.';
        }

        if ($studentCode === '') {
            $errors[] = 'Mã sinh viên không được để trống.';
        }

        if ($fullName === '') {
            $errors[] = 'Họ tên không được để trống.';
        }

        if (!in_array($gender, ['male', 'female', 'other'], true)) {
            $errors[] = 'Giới tính không hợp lệ.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Username đã tồn tại.';
        }

        if ($email !== '') {
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);

            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'Email đã tồn tại.';
            }
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE student_code = :student_code");
        $stmt->execute(['student_code' => $studentCode]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Mã sinh viên đã tồn tại.';
        }

        if (!empty($errors)) {
            render('auth/register', [
                'title' => 'Student Registration',
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT id FROM roles WHERE role_name = 'Student' LIMIT 1");
            $stmt->execute();
            $studentRoleId = $stmt->fetchColumn();

            if (!$studentRoleId) {
                throw new Exception('Không tìm thấy role Student.');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("
                INSERT INTO users (
                    username,
                    password_hash,
                    email,
                    phone,
                    role_id,
                    status
                )
                VALUES (
                    :username,
                    :password_hash,
                    :email,
                    :phone,
                    :role_id,
                    'active'
                )
            ");

            $stmt->execute([
                'username' => $username,
                'password_hash' => $passwordHash,
                'email' => $email !== '' ? $email : null,
                'phone' => $phone !== '' ? $phone : null,
                'role_id' => $studentRoleId
            ]);

            $userId = $db->lastInsertId();

            $stmt = $db->prepare("
                INSERT INTO students (
                    user_id,
                    student_code,
                    full_name,
                    gender,
                    dob,
                    faculty,
                    program,
                    priority_type,
                    address
                )
                VALUES (
                    :user_id,
                    :student_code,
                    :full_name,
                    :gender,
                    :dob,
                    :faculty,
                    :program,
                    'none',
                    :address
                )
            ");

            $stmt->execute([
                'user_id' => $userId,
                'student_code' => $studentCode,
                'full_name' => $fullName,
                'gender' => $gender,
                'dob' => $dob !== '' ? $dob : null,
                'faculty' => $faculty !== '' ? $faculty : null,
                'program' => $program !== '' ? $program : null,
                'address' => $address !== '' ? $address : null
            ]);

            $studentId = $db->lastInsertId();

            $stmt = $db->prepare("
                INSERT INTO audit_logs (
                    user_id,
                    action,
                    table_name,
                    record_id,
                    old_value,
                    new_value,
                    ip_address,
                    user_agent
                )
                VALUES (
                    :user_id,
                    'create',
                    'students',
                    :record_id,
                    NULL,
                    :new_value,
                    :ip_address,
                    :user_agent
                )
            ");

            $stmt->execute([
                'user_id' => $userId,
                'record_id' => $studentId,
                'new_value' => 'Student self-registration: ' . $studentCode,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);

            $db->commit();

            redirectTo('login&registered=1');
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            render('auth/register', [
                'title' => 'Student Registration',
                'errors' => ['Đăng ký thất bại: ' . $e->getMessage()],
                'old' => $_POST
            ]);
        }

        return;
    }

    render('auth/register', [
        'title' => 'Student Registration',
        'errors' => [],
        'old' => []
    ]);
};

$routes['logout'] = function (PDO $db): void {
    Auth::logout();
    redirectTo('login');
};