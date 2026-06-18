<?php

function adminAudit(PDO $db, int $userId, string $action, string $tableName, int $recordId, ?string $oldValue, ?string $newValue): void
{
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
            :action,
            :table_name,
            :record_id,
            :old_value,
            :new_value,
            :ip_address,
            :user_agent
        )
    ");

    $stmt->execute([
        'user_id' => $userId,
        'action' => $action,
        'table_name' => $tableName,
        'record_id' => $recordId,
        'old_value' => $oldValue,
        'new_value' => $newValue,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]);
}

function adminDashboardSummary(PDO $db): array
{
    $totalUsers = (int) $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalStudents = (int) $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $totalManagers = (int) $db->query("
        SELECT COUNT(*)
        FROM users u
        JOIN roles r ON r.id = u.role_id
        WHERE r.role_name = 'Manager'
    ")->fetchColumn();

    $totalBuildings = (int) $db->query("SELECT COUNT(*) FROM buildings")->fetchColumn();
    $totalRooms = (int) $db->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
    $availableRooms = (int) $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'available'")->fetchColumn();
    $maintenanceRooms = (int) $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'maintenance'")->fetchColumn();

    $activeContracts = (int) $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'active'")->fetchColumn();
    $pendingRegistrations = (int) $db->query("SELECT COUNT(*) FROM room_registrations WHERE status = 'pending'")->fetchColumn();
    $unpaidInvoices = (int) $db->query("SELECT COUNT(*) FROM invoices WHERE status IN ('unpaid', 'partially_paid', 'overdue')")->fetchColumn();
    $openMaintenance = (int) $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status IN ('pending', 'in_progress')")->fetchColumn();

    $warningStudents = (int) $db->query("
        SELECT COUNT(*)
        FROM (
            SELECT student_id
            FROM violation_records
            GROUP BY student_id
            HAVING SUM(penalty_points) >= 5
        ) AS warning_students
    ")->fetchColumn();

    return [
        'total_users' => $totalUsers,
        'total_students' => $totalStudents,
        'total_managers' => $totalManagers,
        'total_buildings' => $totalBuildings,
        'total_rooms' => $totalRooms,
        'available_rooms' => $availableRooms,
        'maintenance_rooms' => $maintenanceRooms,
        'active_contracts' => $activeContracts,
        'pending_registrations' => $pendingRegistrations,
        'unpaid_invoices' => $unpaidInvoices,
        'open_maintenance' => $openMaintenance,
        'warning_students' => $warningStudents,
        'users' => $totalUsers,
        'students' => $totalStudents,
        'managers' => $totalManagers,
        'buildings' => $totalBuildings,
        'rooms' => $totalRooms,
    ];
}

function adminGetRoles(PDO $db): array
{
    return $db->query("
        SELECT id, role_name
        FROM roles
        ORDER BY 
            CASE role_name
                WHEN 'Admin' THEN 1
                WHEN 'Manager' THEN 2
                WHEN 'Student' THEN 3
                ELSE 4
            END,
            role_name
    ")->fetchAll();
}

function adminGetUsers(PDO $db, string $roleFilter = '', string $statusFilter = ''): array
{
    $where = [];
    $params = [];

    if ($roleFilter !== '') {
        $where[] = 'r.role_name = :role_name';
        $params['role_name'] = $roleFilter;
    }

    if ($statusFilter !== '') {
        $where[] = 'u.status = :status';
        $params['status'] = $statusFilter;
    }

    $whereSql = '';

    if (!empty($where)) {
        $whereSql = 'WHERE ' . implode(' AND ', $where);
    }

    $stmt = $db->prepare("
        SELECT
            u.id,
            u.username,
            u.email,
            u.phone,
            u.status,
            u.created_at,
            r.role_name,
            s.student_code,
            s.full_name AS student_full_name
        FROM users u
        JOIN roles r ON r.id = u.role_id
        LEFT JOIN students s ON s.user_id = u.id
        $whereSql
        ORDER BY
            CASE r.role_name
                WHEN 'Admin' THEN 1
                WHEN 'Manager' THEN 2
                WHEN 'Student' THEN 3
                ELSE 4
            END,
            u.id DESC
    ");

    $stmt->execute($params);

    return $stmt->fetchAll();
}

function adminUserSummary(PDO $db): array
{
    return [
        'total' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'active' => $db->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn(),
        'inactive' => $db->query("SELECT COUNT(*) FROM users WHERE status <> 'active'")->fetchColumn(),
        'admins' => $db->query("
            SELECT COUNT(*)
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE r.role_name = 'Admin'
        ")->fetchColumn(),
        'managers' => $db->query("
            SELECT COUNT(*)
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE r.role_name = 'Manager'
        ")->fetchColumn(),
        'students' => $db->query("
            SELECT COUNT(*)
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE r.role_name = 'Student'
        ")->fetchColumn(),
    ];
}

$routes['admin/dashboard'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    render('admin/dashboard', [
        'title' => 'Bảng điều khiển quản trị',
        'summary' => adminDashboardSummary($db)
    ]);
};

$routes['admin/users'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $roleFilter = trim($_GET['role'] ?? '');
    $statusFilter = trim($_GET['status'] ?? '');

    render('admin/users', [
        'title' => 'Quản lý tài khoản',
        'users' => adminGetUsers($db, $roleFilter, $statusFilter),
        'roles' => adminGetRoles($db),
        'summary' => adminUserSummary($db),
        'roleFilter' => $roleFilter,
        'statusFilter' => $statusFilter,
        'errors' => [],
        'old' => []
    ]);
};

$routes['admin/user-store'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/users');
    }

    $admin = Auth::user();

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $roleId = (int) ($_POST['role_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'active');

    $errors = [];

    if ($username === '') {
        $errors[] = 'Vui lòng nhập username.';
    }

    if ($password === '') {
        $errors[] = 'Vui lòng nhập password.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password nên có ít nhất 6 ký tự.';
    }

    if ($email === '') {
        $errors[] = 'Vui lòng nhập email.';
    }

    if ($roleId <= 0) {
        $errors[] = 'Vui lòng chọn role.';
    }

    if (!in_array($status, ['active', 'inactive'], true)) {
        $errors[] = 'Trạng thái tài khoản không hợp lệ.';
    }

    if ($username !== '') {
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Username đã tồn tại.';
        }
    }

    if ($email !== '') {
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Email đã tồn tại.';
        }
    }

    if ($roleId > 0) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM roles WHERE id = :id");
        $stmt->execute(['id' => $roleId]);

        if ((int) $stmt->fetchColumn() === 0) {
            $errors[] = 'Role không tồn tại.';
        }
    }

    if (!empty($errors)) {
        render('admin/users', [
            'title' => 'Quản lý tài khoản',
            'users' => adminGetUsers($db),
            'roles' => adminGetRoles($db),
            'summary' => adminUserSummary($db),
            'roleFilter' => '',
            'statusFilter' => '',
            'errors' => $errors,
            'old' => $_POST
        ]);
        return;
    }

    try {
        $db->beginTransaction();

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (
                username,
                password_hash,
                email,
                phone,
                role_id,
                status,
                created_at
            )
            VALUES (
                :username,
                :password_hash,
                :email,
                :phone,
                :role_id,
                :status,
                NOW()
            )
        ");

        $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'role_id' => $roleId,
            'status' => $status
        ]);

        $userId = (int) $db->lastInsertId();

        adminAudit($db, (int) $admin['id'], 'create', 'users', $userId, null, 'Admin created user account: ' . $username);

        $db->commit();

        redirectTo('admin/users');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo user thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/users">Quay lại</a>';
        exit;
    }
};

$routes['admin/user-toggle-status'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/users');
    }

    $admin = Auth::user();
    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($userId <= 0) {
        redirectTo('admin/users');
    }

    if ($userId === (int) $admin['id']) {
        echo '<h2>Không thể tự khóa tài khoản của chính mình.</h2>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/users">Quay lại</a>';
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT id, username, status
            FROM users
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $userId]);
        $targetUser = $stmt->fetch();

        if (!$targetUser) {
            throw new Exception('Không tìm thấy user.');
        }

        $oldStatus = $targetUser['status'];
        $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

        $stmt = $db->prepare("
            UPDATE users
            SET status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'status' => $newStatus,
            'id' => $userId
        ]);

        adminAudit($db, (int) $admin['id'], 'update', 'users', $userId, $oldStatus, $newStatus);

        $db->commit();

        redirectTo('admin/users');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật trạng thái user thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/users">Quay lại</a>';
        exit;
    }
};

$routes['admin/user-reset-password'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/users');
    }

    $admin = Auth::user();
    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($userId <= 0) {
        redirectTo('admin/users');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT id, username
            FROM users
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $userId]);
        $targetUser = $stmt->fetch();

        if (!$targetUser) {
            throw new Exception('Không tìm thấy user.');
        }

        $passwordHash = password_hash('password', PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            UPDATE users
            SET password_hash = :password_hash
            WHERE id = :id
        ");

        $stmt->execute([
            'password_hash' => $passwordHash,
            'id' => $userId
        ]);

        adminAudit($db, (int) $admin['id'], 'update', 'users', $userId, null, 'Admin reset password for user: ' . $targetUser['username']);

        $db->commit();

        redirectTo('admin/users');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Reset password thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/users">Quay lại</a>';
        exit;
    }
};

$routes['admin/buildings'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $status = trim($_GET['status'] ?? '');
    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, ['active', 'inactive'], true)) {
        $where = 'WHERE b.status = :status';
        $params['status'] = $status;
    }

    $stmt = $db->prepare("
        SELECT
            b.*,
            COUNT(r.id) AS room_count
        FROM buildings b
        LEFT JOIN rooms r ON r.building_id = b.id
        $where
        GROUP BY b.id
        ORDER BY b.id DESC
    ");

    $stmt->execute($params);

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM buildings")->fetchColumn(),
        'active' => $db->query("SELECT COUNT(*) FROM buildings WHERE status = 'active'")->fetchColumn(),
        'inactive' => $db->query("SELECT COUNT(*) FROM buildings WHERE status = 'inactive'")->fetchColumn(),
    ];

    render('admin/buildings', [
        'title' => 'Tòa nhà',
        'buildings' => $stmt->fetchAll(),
        'summary' => $summary,
        'statusFilter' => $status,
        'errors' => [],
        'old' => []
    ]);
};

$routes['admin/building-store'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/buildings');
    }

    $admin = Auth::user();

    $buildingName = trim($_POST['building_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($buildingName === '' || !in_array($status, ['active', 'inactive'], true)) {
        echo '<h2>Tạo tòa nhà thất bại</h2>';
        echo '<p>Vui lòng nhập đầy đủ tên tòa nhà và trạng thái hợp lệ.</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/buildings">Quay lại</a>';
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO buildings (
                building_name,
                address,
                description,
                status,
                created_at
            )
            VALUES (
                :building_name,
                :address,
                :description,
                :status,
                NOW()
            )
        ");

        $stmt->execute([
            'building_name' => $buildingName,
            'address' => $address !== '' ? $address : null,
            'description' => $description !== '' ? $description : null,
            'status' => $status
        ]);

        $buildingId = (int) $db->lastInsertId();

        adminAudit($db, (int) $admin['id'], 'create', 'buildings', $buildingId, null, 'Created building: ' . $buildingName);

        $db->commit();

        redirectTo('admin/buildings');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo tòa nhà thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/buildings">Quay lại</a>';
        exit;
    }
};

$routes['admin/building-update'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/buildings');
    }

    $admin = Auth::user();

    $id = (int) ($_POST['id'] ?? 0);
    $buildingName = trim($_POST['building_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($id <= 0 || $buildingName === '' || !in_array($status, ['active', 'inactive'], true)) {
        redirectTo('admin/buildings');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE buildings
            SET
                building_name = :building_name,
                address = :address,
                description = :description,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'building_name' => $buildingName,
            'address' => $address !== '' ? $address : null,
            'description' => $description !== '' ? $description : null,
            'status' => $status,
            'id' => $id
        ]);

        adminAudit($db, (int) $admin['id'], 'update', 'buildings', $id, null, 'Updated building: ' . $buildingName);

        $db->commit();

        redirectTo('admin/buildings');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật tòa nhà thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/buildings">Quay lại</a>';
        exit;
    }
};

$routes['admin/rooms'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $buildings = $db->query("
        SELECT id, building_name
        FROM buildings
        WHERE status = 'active'
        ORDER BY building_name
    ")->fetchAll();

    $status = trim($_GET['status'] ?? '');
    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, ['available', 'full', 'maintenance', 'inactive'], true)) {
        $where = 'WHERE r.status = :status';
        $params['status'] = $status;
    }

    $stmt = $db->prepare("
        SELECT
            r.*,
            b.building_name,
            COUNT(c.id) AS current_occupancy
        FROM rooms r
        JOIN buildings b ON b.id = r.building_id
        LEFT JOIN contracts c ON c.room_id = r.id AND c.status = 'active'
        $where
        GROUP BY r.id
        ORDER BY b.building_name, r.room_number
    ");

    $stmt->execute($params);

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM rooms")->fetchColumn(),
        'available' => $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'available'")->fetchColumn(),
        'maintenance' => $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'maintenance'")->fetchColumn(),
        'inactive' => $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'inactive'")->fetchColumn(),
    ];

    render('admin/rooms', [
        'title' => 'Phòng',
        'rooms' => $stmt->fetchAll(),
        'buildings' => $buildings,
        'summary' => $summary,
        'statusFilter' => $status,
        'errors' => [],
        'old' => []
    ]);
};

$routes['admin/room-store'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/rooms');
    }

    $admin = Auth::user();

    $buildingId = (int) ($_POST['building_id'] ?? 0);
    $roomNumber = trim($_POST['room_number'] ?? '');
    $roomType = trim($_POST['room_type'] ?? '');
    $genderType = trim($_POST['gender_type'] ?? '');
    $capacity = (int) ($_POST['capacity'] ?? 0);
    $pricePerMonth = (float) ($_POST['price_per_month'] ?? 0);
    $status = trim($_POST['status'] ?? 'available');

    if (
        $buildingId <= 0 ||
        $roomNumber === '' ||
        $roomType === '' ||
        $genderType === '' ||
        $capacity <= 0 ||
        $pricePerMonth < 0 ||
        !in_array($status, ['available', 'full', 'maintenance', 'inactive'], true)
    ) {
        echo '<h2>Tạo phòng thất bại</h2>';
        echo '<p>Vui lòng nhập đầy đủ và hợp lệ thông tin phòng.</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/rooms">Quay lại</a>';
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO rooms (
                building_id,
                room_number,
                room_type,
                gender_type,
                capacity,
                price_per_month,
                status,
                created_at
            )
            VALUES (
                :building_id,
                :room_number,
                :room_type,
                :gender_type,
                :capacity,
                :price_per_month,
                :status,
                NOW()
            )
        ");

        $stmt->execute([
            'building_id' => $buildingId,
            'room_number' => $roomNumber,
            'room_type' => $roomType,
            'gender_type' => $genderType,
            'capacity' => $capacity,
            'price_per_month' => $pricePerMonth,
            'status' => $status
        ]);

        $roomId = (int) $db->lastInsertId();

        adminAudit($db, (int) $admin['id'], 'create', 'rooms', $roomId, null, 'Created room: ' . $roomNumber);

        $db->commit();

        redirectTo('admin/rooms');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo phòng thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/rooms">Quay lại</a>';
        exit;
    }
};

$routes['admin/room-update'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/rooms');
    }

    $admin = Auth::user();

    $id = (int) ($_POST['id'] ?? 0);
    $buildingId = (int) ($_POST['building_id'] ?? 0);
    $roomNumber = trim($_POST['room_number'] ?? '');
    $roomType = trim($_POST['room_type'] ?? '');
    $genderType = trim($_POST['gender_type'] ?? '');
    $capacity = (int) ($_POST['capacity'] ?? 0);
    $pricePerMonth = (float) ($_POST['price_per_month'] ?? 0);
    $status = trim($_POST['status'] ?? 'available');

    if (
        $id <= 0 ||
        $buildingId <= 0 ||
        $roomNumber === '' ||
        $roomType === '' ||
        $genderType === '' ||
        $capacity <= 0 ||
        $pricePerMonth < 0 ||
        !in_array($status, ['available', 'full', 'maintenance', 'inactive'], true)
    ) {
        redirectTo('admin/rooms');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE rooms
            SET
                building_id = :building_id,
                room_number = :room_number,
                room_type = :room_type,
                gender_type = :gender_type,
                capacity = :capacity,
                price_per_month = :price_per_month,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'building_id' => $buildingId,
            'room_number' => $roomNumber,
            'room_type' => $roomType,
            'gender_type' => $genderType,
            'capacity' => $capacity,
            'price_per_month' => $pricePerMonth,
            'status' => $status,
            'id' => $id
        ]);

        adminAudit($db, (int) $admin['id'], 'update', 'rooms', $id, null, 'Updated room: ' . $roomNumber);

        $db->commit();

        redirectTo('admin/rooms');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật phòng thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/rooms">Quay lại</a>';
        exit;
    }
};

$routes['admin/semesters'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $status = trim($_GET['status'] ?? '');
    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, ['upcoming', 'open', 'closed'], true)) {
        $where = 'WHERE status = :status';
        $params['status'] = $status;
    }

    $stmt = $db->prepare("
        SELECT *
        FROM semesters
        $where
        ORDER BY start_date DESC, id DESC
    ");

    $stmt->execute($params);

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM semesters")->fetchColumn(),
        'upcoming' => $db->query("SELECT COUNT(*) FROM semesters WHERE status = 'upcoming'")->fetchColumn(),
        'open' => $db->query("SELECT COUNT(*) FROM semesters WHERE status = 'open'")->fetchColumn(),
        'closed' => $db->query("SELECT COUNT(*) FROM semesters WHERE status = 'closed'")->fetchColumn(),
    ];

    render('admin/semesters', [
        'title' => 'Học kỳ',
        'semesters' => $stmt->fetchAll(),
        'summary' => $summary,
        'statusFilter' => $status,
        'errors' => [],
        'old' => []
    ]);
};

$routes['admin/semester-store'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/semesters');
    }

    $admin = Auth::user();

    $semesterName = trim($_POST['semester_name'] ?? '');
    $academicYear = trim($_POST['academic_year'] ?? '');
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    $status = trim($_POST['status'] ?? 'upcoming');

    if (
        $semesterName === '' ||
        $academicYear === '' ||
        $startDate === '' ||
        $endDate === '' ||
        !in_array($status, ['upcoming', 'open', 'closed'], true)
    ) {
        echo '<h2>Tạo học kỳ thất bại</h2>';
        echo '<p>Vui lòng nhập đầy đủ thông tin học kỳ.</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/semesters">Quay lại</a>';
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO semesters (
                semester_name,
                academic_year,
                start_date,
                end_date,
                status,
                created_at
            )
            VALUES (
                :semester_name,
                :academic_year,
                :start_date,
                :end_date,
                :status,
                NOW()
            )
        ");

        $stmt->execute([
            'semester_name' => $semesterName,
            'academic_year' => $academicYear,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status
        ]);

        $semesterId = (int) $db->lastInsertId();

        adminAudit($db, (int) $admin['id'], 'create', 'semesters', $semesterId, null, 'Created semester: ' . $semesterName);

        $db->commit();

        redirectTo('admin/semesters');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo học kỳ thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/semesters">Quay lại</a>';
        exit;
    }
};

$routes['admin/semester-update'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/semesters');
    }

    $admin = Auth::user();

    $id = (int) ($_POST['id'] ?? 0);
    $semesterName = trim($_POST['semester_name'] ?? '');
    $academicYear = trim($_POST['academic_year'] ?? '');
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    $status = trim($_POST['status'] ?? 'upcoming');

    if (
        $id <= 0 ||
        $semesterName === '' ||
        $academicYear === '' ||
        $startDate === '' ||
        $endDate === '' ||
        !in_array($status, ['upcoming', 'open', 'closed'], true)
    ) {
        redirectTo('admin/semesters');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE semesters
            SET
                semester_name = :semester_name,
                academic_year = :academic_year,
                start_date = :start_date,
                end_date = :end_date,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'semester_name' => $semesterName,
            'academic_year' => $academicYear,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'id' => $id
        ]);

        adminAudit($db, (int) $admin['id'], 'update', 'semesters', $id, null, 'Updated semester: ' . $semesterName);

        $db->commit();

        redirectTo('admin/semesters');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật học kỳ thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/semesters">Quay lại</a>';
        exit;
    }
};

$routes['admin/services'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $status = trim($_GET['status'] ?? '');
    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, ['active', 'inactive'], true)) {
        $where = 'WHERE status = :status';
        $params['status'] = $status;
    }

    $stmt = $db->prepare("
        SELECT *
        FROM services
        $where
        ORDER BY id DESC
    ");

    $stmt->execute($params);

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM services")->fetchColumn(),
        'active' => $db->query("SELECT COUNT(*) FROM services WHERE status = 'active'")->fetchColumn(),
        'inactive' => $db->query("SELECT COUNT(*) FROM services WHERE status = 'inactive'")->fetchColumn(),
    ];

    render('admin/services', [
        'title' => 'Dịch vụ',
        'services' => $stmt->fetchAll(),
        'summary' => $summary,
        'statusFilter' => $status,
        'errors' => [],
        'old' => []
    ]);
};

$routes['admin/service-store'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/services');
    }

    $admin = Auth::user();

    $serviceName = trim($_POST['service_name'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $defaultPrice = (float) ($_POST['default_price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($serviceName === '' || $defaultPrice < 0 || !in_array($status, ['active', 'inactive'], true)) {
        echo '<h2>Tạo dịch vụ thất bại</h2>';
        echo '<p>Vui lòng nhập đầy đủ thông tin dịch vụ.</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/services">Quay lại</a>';
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO services (
                service_name,
                unit,
                unit_price,
                default_price,
                description,
                status,
                created_at
            )
            VALUES (
                :service_name,
                :unit,
                :unit_price,
                :default_price,
                :description,
                :status,
                NOW()
            )
        ");

        $stmt->execute([
            'service_name' => $serviceName,
            'unit' => $unit !== '' ? $unit : null,
            'unit_price' => $defaultPrice,
            'default_price' => $defaultPrice,
            'description' => $description !== '' ? $description : null,
            'status' => $status
        ]);

        $serviceId = (int) $db->lastInsertId();

        adminAudit($db, (int) $admin['id'], 'create', 'services', $serviceId, null, 'Created service: ' . $serviceName);

        $db->commit();

        redirectTo('admin/services');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo dịch vụ thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/services">Quay lại</a>';
        exit;
    }
};

$routes['admin/service-update'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/services');
    }

    $admin = Auth::user();

    $id = (int) ($_POST['id'] ?? 0);
    $serviceName = trim($_POST['service_name'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $defaultPrice = (float) ($_POST['default_price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($id <= 0 || $serviceName === '' || $defaultPrice < 0 || !in_array($status, ['active', 'inactive'], true)) {
        redirectTo('admin/services');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE services
            SET
                service_name = :service_name,
                unit = :unit,
                unit_price = :unit_price,
                default_price = :default_price,
                description = :description,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'service_name' => $serviceName,
            'unit' => $unit !== '' ? $unit : null,
            'unit_price' => $defaultPrice,
            'default_price' => $defaultPrice,
            'description' => $description !== '' ? $description : null,
            'status' => $status,
            'id' => $id
        ]);

        adminAudit($db, (int) $admin['id'], 'update', 'services', $id, null, 'Updated service: ' . $serviceName);

        $db->commit();

        redirectTo('admin/services');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật dịch vụ thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/services">Quay lại</a>';
        exit;
    }
};

if (!function_exists('adminAuditSafe')) {
    function adminAuditSafe(PDO $db, int $userId, string $action, string $tableName, int $recordId, ?string $oldValue, ?string $newValue): void
    {
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
                :action,
                :table_name,
                :record_id,
                :old_value,
                :new_value,
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }
}

$routes['admin/students'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $statusFilter = trim($_GET['status'] ?? '');
    $where = '';
    $params = [];

    if ($statusFilter !== '' && in_array($statusFilter, ['active', 'inactive'], true)) {
        $where = 'WHERE s.status = :status';
        $params['status'] = $statusFilter;
    }

    $stmt = $db->prepare("
        SELECT
            s.*,
            u.username,
            u.email,
            u.phone,
            u.status AS user_status
        FROM students s
        LEFT JOIN users u ON u.id = s.user_id
        $where
        ORDER BY s.id DESC
    ");
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    $studentUsers = $db->query("
        SELECT
            u.id,
            u.username,
            u.email
        FROM users u
        JOIN roles r ON r.id = u.role_id
        LEFT JOIN students s ON s.user_id = u.id
        WHERE r.role_name = 'Student'
          AND s.id IS NULL
        ORDER BY u.username
    ")->fetchAll();

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
        'active' => $db->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn(),
        'inactive' => $db->query("SELECT COUNT(*) FROM students WHERE status = 'inactive'")->fetchColumn(),
        'missing_profile' => count($studentUsers),
    ];

    render('admin/students', [
        'title' => 'Quản lý sinh viên',
        'students' => $students,
        'studentUsers' => $studentUsers,
        'summary' => $summary,
        'statusFilter' => $statusFilter,
        'errors' => [],
        'old' => []
    ]);
};

$routes['admin/student-store'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/students');
    }

    $admin = Auth::user();

    $userId = (int) ($_POST['user_id'] ?? 0);
    $studentCode = trim($_POST['student_code'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $priorityType = trim($_POST['priority_type'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    $errors = [];

    if ($userId <= 0) {
        $errors[] = 'Vui lòng chọn user Student.';
    }

    if ($studentCode === '') {
        $errors[] = 'Vui lòng nhập mã sinh viên.';
    }

    if ($fullName === '') {
        $errors[] = 'Vui lòng nhập họ tên sinh viên.';
    }

    if (!in_array($gender, ['male', 'female', 'other'], true)) {
        $errors[] = 'Giới tính không hợp lệ.';
    }

    if (!in_array($status, ['active', 'inactive'], true)) {
        $errors[] = 'Trạng thái không hợp lệ.';
    }

    if ($studentCode !== '') {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM students
            WHERE student_code = :student_code
        ");
        $stmt->execute(['student_code' => $studentCode]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Mã sinh viên đã tồn tại.';
        }
    }

    if ($userId > 0) {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM students
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'User này đã có hồ sơ sinh viên.';
        }
    }

    if (!empty($errors)) {
        $stmt = $db->query("
            SELECT
                s.*,
                u.username,
                u.email,
                u.phone,
                u.status AS user_status
            FROM students s
            LEFT JOIN users u ON u.id = s.user_id
            ORDER BY s.id DESC
        ");

        $studentUsers = $db->query("
            SELECT
                u.id,
                u.username,
                u.email
            FROM users u
            JOIN roles r ON r.id = u.role_id
            LEFT JOIN students s ON s.user_id = u.id
            WHERE r.role_name = 'Student'
              AND s.id IS NULL
            ORDER BY u.username
        ")->fetchAll();

        render('admin/students', [
            'title' => 'Quản lý sinh viên',
            'students' => $stmt->fetchAll(),
            'studentUsers' => $studentUsers,
            'summary' => [
                'total' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
                'active' => $db->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn(),
                'inactive' => $db->query("SELECT COUNT(*) FROM students WHERE status = 'inactive'")->fetchColumn(),
                'missing_profile' => count($studentUsers),
            ],
            'statusFilter' => '',
            'errors' => $errors,
            'old' => $_POST
        ]);
        return;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO students (
                user_id,
                student_code,
                full_name,
                gender,
                faculty,
                program,
                priority_type,
                status,
                created_at
            )
            VALUES (
                :user_id,
                :student_code,
                :full_name,
                :gender,
                :faculty,
                :program,
                :priority_type,
                :status,
                NOW()
            )
        ");

        $stmt->execute([
            'user_id' => $userId,
            'student_code' => $studentCode,
            'full_name' => $fullName,
            'gender' => $gender,
            'faculty' => $faculty !== '' ? $faculty : null,
            'program' => $program !== '' ? $program : null,
            'priority_type' => $priorityType !== '' ? $priorityType : null,
            'status' => $status
        ]);

        $studentId = (int) $db->lastInsertId();

        adminAuditSafe($db, (int) $admin['id'], 'create', 'students', $studentId, null, 'Created student profile: ' . $studentCode);

        $db->commit();

        redirectTo('admin/students');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo hồ sơ sinh viên thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/students">Quay lại</a>';
        exit;
    }
};

$routes['admin/student-update'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('admin/students');
    }

    $admin = Auth::user();

    $id = (int) ($_POST['id'] ?? 0);
    $studentCode = trim($_POST['student_code'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $priorityType = trim($_POST['priority_type'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if (
        $id <= 0 ||
        $studentCode === '' ||
        $fullName === '' ||
        !in_array($gender, ['male', 'female', 'other'], true) ||
        !in_array($status, ['active', 'inactive'], true)
    ) {
        redirectTo('admin/students');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE students
            SET
                student_code = :student_code,
                full_name = :full_name,
                gender = :gender,
                faculty = :faculty,
                program = :program,
                priority_type = :priority_type,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'student_code' => $studentCode,
            'full_name' => $fullName,
            'gender' => $gender,
            'faculty' => $faculty !== '' ? $faculty : null,
            'program' => $program !== '' ? $program : null,
            'priority_type' => $priorityType !== '' ? $priorityType : null,
            'status' => $status,
            'id' => $id
        ]);

        adminAuditSafe($db, (int) $admin['id'], 'update', 'students', $id, null, 'Updated student profile: ' . $studentCode);

        $db->commit();

        redirectTo('admin/students');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật hồ sơ sinh viên thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=admin/students">Quay lại</a>';
        exit;
    }
};

$routes['admin/audit-logs'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $actionFilter = trim($_GET['action'] ?? '');
    $tableFilter = trim($_GET['table'] ?? '');

    $where = [];
    $params = [];

    if ($actionFilter !== '') {
        $where[] = 'al.action = :action';
        $params['action'] = $actionFilter;
    }

    if ($tableFilter !== '') {
        $where[] = 'al.table_name = :table_name';
        $params['table_name'] = $tableFilter;
    }

    $whereSql = '';
    if (!empty($where)) {
        $whereSql = 'WHERE ' . implode(' AND ', $where);
    }

    $stmt = $db->prepare("
        SELECT
            al.*,
            u.username
        FROM audit_logs al
        LEFT JOIN users u ON u.id = al.user_id
        $whereSql
        ORDER BY al.id DESC
        LIMIT 300
    ");

    $stmt->execute($params);

    $actions = $db->query("
        SELECT DISTINCT action
        FROM audit_logs
        ORDER BY action
    ")->fetchAll();

    $tables = $db->query("
        SELECT DISTINCT table_name
        FROM audit_logs
        ORDER BY table_name
    ")->fetchAll();

    render('admin/audit_logs', [
        'title' => 'Nhật ký hệ thống',
        'logs' => $stmt->fetchAll(),
        'actions' => $actions,
        'tables' => $tables,
        'actionFilter' => $actionFilter,
        'tableFilter' => $tableFilter
    ]);
};

$routes['admin/reports'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $overview = [
        'total_students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
        'active_contracts' => $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'active'")->fetchColumn(),
        'ended_contracts' => $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'ended'")->fetchColumn(),
        'pending_registrations' => $db->query("SELECT COUNT(*) FROM room_registrations WHERE status = 'pending'")->fetchColumn(),
        'unpaid_invoices' => $db->query("SELECT COUNT(*) FROM invoices WHERE status IN ('unpaid', 'partially_paid', 'overdue')")->fetchColumn(),
        'paid_invoices' => $db->query("SELECT COUNT(*) FROM invoices WHERE status = 'paid'")->fetchColumn(),
        'open_maintenance' => $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status IN ('pending', 'in_progress')")->fetchColumn(),
    ];

    $occupancyByBuilding = $db->query("
        SELECT
            b.building_name,
            COUNT(DISTINCT r.id) AS total_rooms,
            COALESCE(SUM(r.capacity), 0) AS total_capacity,
            COUNT(c.id) AS active_occupancy
        FROM buildings b
        LEFT JOIN rooms r ON r.building_id = b.id
        LEFT JOIN contracts c ON c.room_id = r.id AND c.status = 'active'
        GROUP BY b.id, b.building_name
        ORDER BY b.building_name
    ")->fetchAll();

    $invoiceSummary = $db->query("
        SELECT
            status,
            COUNT(*) AS invoice_count,
            COALESCE(SUM(total_amount), 0) AS total_amount,
            COALESCE(SUM(paid_amount), 0) AS paid_amount
        FROM invoices
        GROUP BY status
        ORDER BY status
    ")->fetchAll();

    $topViolationStudents = $db->query("
        SELECT
            s.student_code,
            s.full_name,
            COALESCE(SUM(vr.penalty_points), 0) AS total_points,
            COUNT(vr.id) AS violation_count
        FROM students s
        JOIN violation_records vr ON vr.student_id = s.id
        GROUP BY s.id, s.student_code, s.full_name
        ORDER BY total_points DESC, violation_count DESC
        LIMIT 10
    ")->fetchAll();

    render('admin/reports', [
        'title' => 'Báo cáo',
        'overview' => $overview,
        'occupancyByBuilding' => $occupancyByBuilding,
        'invoiceSummary' => $invoiceSummary,
        'topViolationStudents' => $topViolationStudents
    ]);
};
