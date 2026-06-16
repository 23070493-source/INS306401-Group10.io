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
$routes['profile'] = function (PDO $db): void {
    Auth::requireLogin();

    $user = Auth::user();
    $studentProfile = null;

    if ($user['role_name'] === 'Student') {
        $stmt = $db->prepare("
            SELECT *
            FROM students
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $user['id']
        ]);

        $studentProfile = $stmt->fetch();
    }

    render('profile/index', [
        'title' => 'My Profile',
        'user' => $user,
        'studentProfile' => $studentProfile,
        'profileErrors' => [],
        'passwordErrors' => [],
        'success' => $_GET['success'] ?? null
    ]);
};

$routes['profile/update'] = function (PDO $db): void {
    Auth::requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('profile');
    }

    $user = Auth::user();

    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $errors = [];
    $avatarPath = $user['avatar'] ?? null;

    if ($email === '') {
        $errors[] = 'Vui lòng nhập email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }

    if ($email !== '') {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM users
            WHERE email = :email
              AND id <> :id
        ");

        $stmt->execute([
            'email' => $email,
            'id' => $user['id']
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Email này đã được tài khoản khác sử dụng.';
        }
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload avatar thất bại.';
        } else {
            $maxSize = 5 * 1024 * 1024;

            if ($_FILES['avatar']['size'] > $maxSize) {
                $errors[] = 'Avatar không được vượt quá 5MB.';
            }

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
            finfo_close($finfo);

            if (!array_key_exists($mimeType, $allowedMimeTypes)) {
                $errors[] = 'Avatar chỉ được dùng định dạng JPG, PNG hoặc WEBP.';
            }

            if (empty($errors)) {
                $uploadDir = dirname(__DIR__) . '/public/uploads/avatars';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = $allowedMimeTypes[$mimeType];
                $fileName = 'avatar_' . $user['id'] . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
                $targetPath = $uploadDir . '/' . $fileName;

                if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                    $errors[] = 'Không thể lưu avatar.';
                } else {
                    $avatarPath = 'uploads/avatars/' . $fileName;
                }
            }
        }
    }

    $studentProfile = null;

    if ($user['role_name'] === 'Student') {
        $stmt = $db->prepare("
            SELECT *
            FROM students
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $user['id']
        ]);

        $studentProfile = $stmt->fetch();
    }

    if (!empty($errors)) {
        render('profile/index', [
            'title' => 'My Profile',
            'user' => $user,
            'studentProfile' => $studentProfile,
            'profileErrors' => $errors,
            'passwordErrors' => [],
            'success' => null
        ]);
        return;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE users
            SET
                email = :email,
                phone = :phone,
                avatar = :avatar
            WHERE id = :id
        ");

        $stmt->execute([
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'avatar' => $avatarPath,
            'id' => $user['id']
        ]);

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
                'update',
                'users',
                :record_id,
                NULL,
                :new_value,
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $user['id'],
            'record_id' => $user['id'],
            'new_value' => 'User updated profile information',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);

        $db->commit();

        Auth::refreshUserSession();

        redirectTo('profile&success=profile');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật profile thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=profile">Quay lại</a>';
        exit;
    }
};

$routes['profile/password'] = function (PDO $db): void {
    Auth::requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('profile');
    }

    $user = Auth::user();

    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    $errors = [];

    if ($currentPassword === '') {
        $errors[] = 'Vui lòng nhập mật khẩu hiện tại.';
    }

    if ($newPassword === '') {
        $errors[] = 'Vui lòng nhập mật khẩu mới.';
    } elseif (strlen($newPassword) < 6) {
        $errors[] = 'Mật khẩu mới nên có ít nhất 6 ký tự.';
    }

    if ($confirmPassword === '') {
        $errors[] = 'Vui lòng nhập lại mật khẩu mới.';
    }

    if ($newPassword !== '' && $confirmPassword !== '' && $newPassword !== $confirmPassword) {
        $errors[] = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
    }

    $stmt = $db->prepare("
        SELECT password_hash
        FROM users
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        'id' => $user['id']
    ]);

    $account = $stmt->fetch();

    if (!$account || !password_verify($currentPassword, $account['password_hash'])) {
        $errors[] = 'Mật khẩu hiện tại không đúng.';
    }

    $studentProfile = null;

    if ($user['role_name'] === 'Student') {
        $stmt = $db->prepare("
            SELECT *
            FROM students
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $user['id']
        ]);

        $studentProfile = $stmt->fetch();
    }

    if (!empty($errors)) {
        render('profile/index', [
            'title' => 'My Profile',
            'user' => $user,
            'studentProfile' => $studentProfile,
            'profileErrors' => [],
            'passwordErrors' => $errors,
            'success' => null
        ]);
        return;
    }

    try {
        $db->beginTransaction();

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            UPDATE users
            SET password_hash = :password_hash
            WHERE id = :id
        ");

        $stmt->execute([
            'password_hash' => $newPasswordHash,
            'id' => $user['id']
        ]);

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
                'update',
                'users',
                :record_id,
                NULL,
                :new_value,
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $user['id'],
            'record_id' => $user['id'],
            'new_value' => 'User changed password',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);

        $db->commit();

        redirectTo('profile&success=password');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Đổi mật khẩu thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=profile">Quay lại</a>';
        exit;
    }
};