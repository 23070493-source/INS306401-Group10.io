<?php

if (!function_exists('authColumnExists')) {
    function authColumnExists(PDO $db, string $table, string $column): bool
    {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name
        ");

        $stmt->execute([
            'table_name' => $table,
            'column_name' => $column
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

if (!function_exists('authInsertDynamic')) {
    function authInsertDynamic(PDO $db, string $table, array $data): int
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if (authColumnExists($db, $table, $column)) {
                $filtered[$column] = $value;
            }
        }

        if (empty($filtered)) {
            throw new Exception('No valid columns to insert into ' . $table);
        }

        $columns = array_keys($filtered);
        $placeholders = array_map(fn ($column) => ':' . $column, $columns);

        $sql = "
            INSERT INTO {$table} (
                " . implode(', ', $columns) . "
            )
            VALUES (
                " . implode(', ', $placeholders) . "
            )
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($filtered);

        return (int) $db->lastInsertId();
    }
}

if (!function_exists('authUpdateDynamic')) {
    function authUpdateDynamic(PDO $db, string $table, array $data, string $whereColumn, mixed $whereValue): void
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if (authColumnExists($db, $table, $column)) {
                $filtered[$column] = $value;
            }
        }

        if (empty($filtered)) {
            return;
        }

        $sets = [];

        foreach (array_keys($filtered) as $column) {
            $sets[] = "{$column} = :{$column}";
        }

        $filtered['where_value'] = $whereValue;

        $sql = "
            UPDATE {$table}
            SET " . implode(', ', $sets) . "
            WHERE {$whereColumn} = :where_value
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($filtered);
    }
}

if (!function_exists('authGetStudentRoleId')) {
    function authGetStudentRoleId(PDO $db): int
    {
        $stmt = $db->prepare("
            SELECT id
            FROM roles
            WHERE LOWER(role_name) = 'student'
            LIMIT 1
        ");

        $stmt->execute();
        $roleId = $stmt->fetchColumn();

        if (!$roleId) {
            throw new Exception('Không tìm thấy role Student trong bảng roles.');
        }

        return (int) $roleId;
    }
}

$routes['home'] = function (PDO $db): void {
    if (Auth::check()) {
        redirectTo(Auth::dashboardRoute());
    }

    redirectTo('login');
};

$routes['login'] = function (PDO $db): void {
    if (Auth::check()) {
        redirectTo(Auth::dashboardRoute());
    }

    $error = null;
    $success = $_GET['success'] ?? null;
    $old = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $old = [
            'username' => $username
        ];

        if ($username === '' || $password === '') {
            $error = 'Vui lòng nhập username và password.';
        } elseif (Auth::attempt($username, $password)) {
            redirectTo(Auth::dashboardRoute());
        } else {
            $error = 'Username hoặc password không đúng.';
        }
    }

    render('auth/login', [
        'title' => 'Login',
        'error' => $error,
        'success' => $success,
        'old' => $old
    ]);
};

$routes['logout'] = function (PDO $db): void {
    Auth::logout();
    redirectTo('login');
};

$routes['register'] = function (PDO $db): void {
    if (Auth::check()) {
        redirectTo(Auth::dashboardRoute());
    }

    $errors = [];
    $success = null;
    $old = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        $studentCode = trim($_POST['student_code'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $dob = trim($_POST['dob'] ?? '');
        $faculty = trim($_POST['faculty'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $priorityType = trim($_POST['priority_type'] ?? 'none');

        $old = $_POST;

        if ($username === '') {
            $errors[] = 'Vui lòng nhập username.';
        }

        if ($email === '') {
            $errors[] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        }

        if ($password === '') {
            $errors[] = 'Vui lòng nhập password.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password phải có ít nhất 6 ký tự.';
        }

        if ($confirmPassword === '') {
            $errors[] = 'Vui lòng xác nhận password.';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Password xác nhận không khớp.';
        }

        if ($studentCode === '') {
            $errors[] = 'Vui lòng nhập mã sinh viên.';
        }

        if ($fullName === '') {
            $errors[] = 'Vui lòng nhập họ tên.';
        }

        if ($gender === '') {
            $errors[] = 'Vui lòng chọn giới tính.';
        }

        if (empty($errors)) {
            $stmt = $db->prepare("
                SELECT COUNT(*)
                FROM users
                WHERE username = :username
            ");

            $stmt->execute([
                'username' => $username
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'Username đã tồn tại.';
            }
        }

        if (empty($errors) && $email !== '') {
            $stmt = $db->prepare("
                SELECT COUNT(*)
                FROM users
                WHERE email = :email
            ");

            $stmt->execute([
                'email' => $email
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'Email đã tồn tại.';
            }
        }

        if (empty($errors) && $studentCode !== '') {
            $stmt = $db->prepare("
                SELECT COUNT(*)
                FROM students
                WHERE student_code = :student_code
            ");

            $stmt->execute([
                'student_code' => $studentCode
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'Mã sinh viên đã tồn tại.';
            }
        }

        if (empty($errors)) {
            try {
                $db->beginTransaction();

                $studentRoleId = authGetStudentRoleId($db);
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $userId = authInsertDynamic($db, 'users', [
                    'role_id' => $studentRoleId,
                    'username' => $username,
                    'password_hash' => $passwordHash,
                    'email' => $email,
                    'phone' => $phone !== '' ? $phone : null,
                    'avatar' => null,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                authInsertDynamic($db, 'students', [
                    'user_id' => $userId,
                    'student_code' => $studentCode,
                    'full_name' => $fullName,
                    'gender' => $gender,
                    'dob' => $dob !== '' ? $dob : null,
                    'faculty' => $faculty !== '' ? $faculty : null,
                    'program' => $program !== '' ? $program : null,
                    'address' => $address !== '' ? $address : null,
                    'priority_type' => $priorityType !== '' ? $priorityType : 'none',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $db->commit();

                redirectTo('login&success=' . urlencode('Đăng ký thành công. Bạn có thể đăng nhập.'));
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }

                $errors[] = 'Đăng ký thất bại: ' . $e->getMessage();
            }
        }
    }

    render('auth/register', [
        'title' => 'Register',
        'errors' => $errors,
        'success' => $success,
        'old' => $old
    ]);
};

$routes['forgot-password'] = function (PDO $db): void {
    if (Auth::check()) {
        redirectTo(Auth::dashboardRoute());
    }

    $errors = [];
    $success = null;
    $old = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        $old = [
            'username' => $username,
            'email' => $email
        ];

        if ($username === '') {
            $errors[] = 'Vui lòng nhập username.';
        }

        if ($email === '') {
            $errors[] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        }

        if ($newPassword === '') {
            $errors[] = 'Vui lòng nhập mật khẩu mới.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        }

        if ($confirmPassword === '') {
            $errors[] = 'Vui lòng xác nhận mật khẩu mới.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp.';
        }

        if (empty($errors)) {
            $stmt = $db->prepare("
                SELECT 
                    id,
                    username,
                    email,
                    status
                FROM users
                WHERE username = :username
                  AND email = :email
                LIMIT 1
            ");

            $stmt->execute([
                'username' => $username,
                'email' => $email
            ]);

            $user = $stmt->fetch();

            if (!$user) {
                $errors[] = 'Không tìm thấy tài khoản khớp với username và email.';
            } elseif ($user['status'] !== 'active') {
                $errors[] = 'Tài khoản này hiện không active. Vui lòng liên hệ Admin.';
            } else {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

                $stmt = $db->prepare("
                    UPDATE users
                    SET password_hash = :password_hash
                    WHERE id = :id
                ");

                $stmt->execute([
                    'password_hash' => $newHash,
                    'id' => $user['id']
                ]);

                $success = 'Đổi mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.';
                $old = [];
            }
        }
    }

    render('auth/forgot_password', [
        'title' => 'Forgot Password',
        'errors' => $errors,
        'success' => $success,
        'old' => $old
    ]);
};

$routes['profile'] = function (PDO $db): void {
    Auth::requireLogin();

    $user = Auth::user();
    $errors = [];
    $success = $_GET['success'] ?? null;

    $student = null;

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

        $student = $stmt->fetch();
    }

    render('profile/index', [
        'title' => 'My Profile',
        'user' => $user,
        'student' => $student,
        'errors' => $errors,
        'success' => $success
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

    if ($email === '') {
        $errors[] = 'Vui lòng nhập email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }

    if (empty($errors)) {
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
            $errors[] = 'Email này đã được sử dụng bởi tài khoản khác.';
        }
    }

    $avatarPath = $user['avatar'] ?? null;

    if (empty($errors) && isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload avatar thất bại.';
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileType = mime_content_type($_FILES['avatar']['tmp_name']);

            if (!in_array($fileType, $allowedTypes, true)) {
                $errors[] = 'Avatar chỉ được phép là JPG, PNG, WEBP hoặc GIF.';
            } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Avatar không được vượt quá 2MB.';
            } else {
                $uploadDir = dirname(__DIR__) . '/public/uploads/avatars';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $fileName = 'avatar_user_' . $user['id'] . '_' . time() . '.' . strtolower($extension);
                $targetPath = $uploadDir . '/' . $fileName;

                if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                    $errors[] = 'Không thể lưu avatar.';
                } else {
                    $avatarPath = 'uploads/avatars/' . $fileName;
                }
            }
        }
    }

    if (!empty($errors)) {
        $student = null;

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

            $student = $stmt->fetch();
        }

        render('profile/index', [
            'title' => 'My Profile',
            'user' => $user,
            'student' => $student,
            'errors' => $errors,
            'success' => null
        ]);
        return;
    }

    authUpdateDynamic($db, 'users', [
        'email' => $email,
        'phone' => $phone !== '' ? $phone : null,
        'avatar' => $avatarPath
    ], 'id', $user['id']);

    Auth::refreshUserSession();

    redirectTo('profile&success=' . urlencode('Cập nhật hồ sơ thành công.'));
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
        $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
    }

    if ($confirmPassword === '') {
        $errors[] = 'Vui lòng xác nhận mật khẩu mới.';
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            SELECT password_hash
            FROM users
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $user['id']
        ]);

        $currentHash = $stmt->fetchColumn();

        if (!$currentHash || !password_verify($currentPassword, $currentHash)) {
            $errors[] = 'Mật khẩu hiện tại không đúng.';
        }
    }

    if (!empty($errors)) {
        $student = null;

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

            $student = $stmt->fetch();
        }

        render('profile/index', [
            'title' => 'My Profile',
            'user' => $user,
            'student' => $student,
            'errors' => $errors,
            'success' => null
        ]);
        return;
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $db->prepare("
        UPDATE users
        SET password_hash = :password_hash
        WHERE id = :id
    ");

    $stmt->execute([
        'password_hash' => $newHash,
        'id' => $user['id']
    ]);

    redirectTo('profile&success=' . urlencode('Đổi mật khẩu thành công.'));
};