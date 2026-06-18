<?php
$user = Auth::user();
$pageTitle = $title ?? 'Dormitory Manager';
$assetVersion = '20260618-ui-i18n-3';

$currentRoute = $_GET['route'] ?? 'home';

$roleName = $user['role_name'] ?? 'Guest';

$roleClass = 'guest';
$roleCss = 'auth.css';
$roleLabel = 'Cổng đăng nhập';
$roleLabelKey = 'guest_portal';
$displayRoleName = 'Khách';
$displayRoleKey = 'guest_role';

if ($roleName === 'Admin') {
    $roleClass = 'admin';
    $roleCss = 'admin.css';
    $roleLabel = 'Trung tâm quản trị';
    $roleLabelKey = 'admin_center';
    $displayRoleName = 'Quản trị viên';
    $displayRoleKey = 'admin_role';
} elseif ($roleName === 'Manager') {
    $roleClass = 'manager';
    $roleCss = 'manager.css';
    $roleLabel = 'Điều phối ký túc xá';
    $roleLabelKey = 'manager_center';
    $displayRoleName = 'Quản lý';
    $displayRoleKey = 'manager_role';
} elseif ($roleName === 'Student') {
    $roleClass = 'student';
    $roleCss = 'student.css';
    $roleLabel = 'Cổng sinh viên';
    $roleLabelKey = 'student_portal';
    $displayRoleName = 'Sinh viên';
    $displayRoleKey = 'student_role';
}

$sidebarAvatar = null;

if ($user && !empty($user['avatar'])) {
    $sidebarAvatar = BASE_URL . '/' . ltrim($user['avatar'], '/');
}

$isActive = function (string $route) use ($currentRoute): string {
    return $currentRoute === $route ? 'active' : '';
};

$isActiveGroup = function (array $routes) use ($currentRoute): string {
    return in_array($currentRoute, $routes, true) ? 'active' : '';
};
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Common design system -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css?v=<?= urlencode($assetVersion) ?>">

    <!-- Role-based theme -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/<?= htmlspecialchars($roleCss) ?>?v=<?= urlencode($assetVersion) ?>">
</head>

<body class="role-<?= htmlspecialchars($roleClass) ?>">

<div class="<?= $user ? 'app app-dashboard' : 'app app-auth' ?>">

    <?php if ($user): ?>
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">K</div>

                <div>
                    <h2>Dormitory Manager</h2>
                    <p data-i18n="<?= htmlspecialchars($roleLabelKey) ?>"><?= htmlspecialchars($roleLabel) ?></p>
                </div>
            </div>

            <div class="user-box">
                <?php if ($sidebarAvatar): ?>
                    <img src="<?= htmlspecialchars($sidebarAvatar) ?>" alt="Avatar" class="sidebar-avatar">
                <?php else: ?>
                    <div class="sidebar-avatar-placeholder">
                        <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
                    </div>
                <?php endif; ?>

                <div class="user-meta">
                    <strong><?= htmlspecialchars($user['username']) ?></strong>
                    <span class="role-pill role-pill-<?= htmlspecialchars($roleClass) ?>" data-i18n="<?= htmlspecialchars($displayRoleKey) ?>">
                        <?= htmlspecialchars($displayRoleName) ?>
                    </span>
                </div>
            </div>

            <div class="language-box">
                <label for="language-select" data-i18n="language">Ngôn ngữ</label>
                <select id="language-select" aria-label="Ngôn ngữ" data-i18n-aria-label="language">
                    <option value="vi" data-i18n="vietnamese">Tiếng Việt</option>
                    <option value="en" data-i18n="english">English</option>
                </select>
            </div>

            <nav class="sidebar-nav">
                <?php if ($user['role_name'] === 'Admin'): ?>
                    <a class="<?= $isActive('admin/dashboard') ?>" href="<?= BASE_URL ?>/index.php?route=admin/dashboard">
                        <span class="nav-icon">▦</span>
                        <span data-i18n="dashboard">Bảng điều khiển</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/users']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/users">
                        <span class="nav-icon">👥</span>
                        <span data-i18n="accounts">Tài khoản</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/students']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/students">
                        <span class="nav-icon">🎓</span>
                        <span data-i18n="students">Sinh viên</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/buildings']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings">
                        <span class="nav-icon">🏢</span>
                        <span data-i18n="buildings">Tòa nhà</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/rooms']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
                        <span class="nav-icon">🚪</span>
                        <span data-i18n="rooms">Phòng</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/semesters']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters">
                        <span class="nav-icon">📅</span>
                        <span data-i18n="semesters">Học kỳ</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/services']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/services">
                        <span class="nav-icon">🧾</span>
                        <span data-i18n="services">Dịch vụ</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/audit-logs']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/audit-logs">
                        <span class="nav-icon">🛡</span>
                        <span data-i18n="audit_logs">Nhật ký hệ thống</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/reports']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/reports">
                        <span class="nav-icon">📊</span>
                        <span data-i18n="reports">Báo cáo</span>
                    </a>

                <?php elseif ($user['role_name'] === 'Manager'): ?>
                    <a class="<?= $isActive('manager/dashboard') ?>" href="<?= BASE_URL ?>/index.php?route=manager/dashboard">
                        <span class="nav-icon">▦</span>
                        <span data-i18n="dashboard">Bảng điều khiển</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/registrations', 'manager/registration-detail']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/registrations">
                        <span class="nav-icon">📝</span>
                        <span data-i18n="room_registrations">Đơn đăng ký phòng</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/contracts']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts">
                        <span class="nav-icon">📄</span>
                        <span data-i18n="contracts">Hợp đồng</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/invoices', 'manager/invoice-create']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices">
                        <span class="nav-icon">🧾</span>
                        <span data-i18n="invoices">Hóa đơn</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/utility-readings']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/utility-readings">
                        <span class="nav-icon">⚡</span>
                        <span data-i18n="utility_readings">Chỉ số điện nước</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/payments']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments">
                        <span class="nav-icon">💳</span>
                        <span data-i18n="payments">Thanh toán</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/maintenance']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance">
                        <span class="nav-icon">🛠</span>
                        <span data-i18n="maintenance">Sửa chữa</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/violations']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/violations">
                        <span class="nav-icon">⚠</span>
                        <span data-i18n="violations">Vi phạm</span>
                    </a>

                <?php elseif ($user['role_name'] === 'Student'): ?>
                    <a class="<?= $isActive('student/dashboard') ?>" href="<?= BASE_URL ?>/index.php?route=student/dashboard">
                        <span class="nav-icon">▦</span>
                        <span data-i18n="dashboard">Bảng điều khiển</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/rooms']) ?>" href="<?= BASE_URL ?>/index.php?route=student/rooms">
                        <span class="nav-icon">🏠</span>
                        <span data-i18n="available_rooms">Phòng còn trống</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/register-room']) ?>" href="<?= BASE_URL ?>/index.php?route=student/register-room">
                        <span class="nav-icon">📝</span>
                        <span data-i18n="register_room">Đăng ký phòng</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/my-registration']) ?>" href="<?= BASE_URL ?>/index.php?route=student/my-registration">
                        <span class="nav-icon">📌</span>
                        <span data-i18n="my_registration_short">Đơn của tôi</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/my-contract']) ?>" href="<?= BASE_URL ?>/index.php?route=student/my-contract">
                        <span class="nav-icon">📄</span>
                        <span data-i18n="my_contract">Hợp đồng của tôi</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/my-invoices', 'student/payment-submit', 'student/pay-invoice']) ?>" href="<?= BASE_URL ?>/index.php?route=student/my-invoices">
                        <span class="nav-icon">🧾</span>
                        <span data-i18n="my_invoices">Hóa đơn của tôi</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/maintenance']) ?>" href="<?= BASE_URL ?>/index.php?route=student/maintenance">
                        <span class="nav-icon">🛠</span>
                        <span data-i18n="maintenance_request">Yêu cầu sửa chữa</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/violations']) ?>" href="<?= BASE_URL ?>/index.php?route=student/violations">
                        <span class="nav-icon">⚠</span>
                        <span data-i18n="my_violations">Vi phạm của tôi</span>
                    </a>
                <?php endif; ?>

                <div class="sidebar-divider"></div>

                <a class="<?= $isActive('profile') ?>" href="<?= BASE_URL ?>/index.php?route=profile">
                    <span class="nav-icon">👤</span>
                    <span data-i18n="profile">Hồ sơ cá nhân</span>
                </a>

                <a class="logout-link" href="<?= BASE_URL ?>/index.php?route=logout">
                    <span class="nav-icon">↪</span>
                    <span data-i18n="logout">Đăng xuất</span>
                </a>
            </nav>
        </aside>
    <?php endif; ?>

    <main class="<?= $user ? 'main-content' : 'auth-content' ?>">
        <?php if (!$user): ?>
            <div class="auth-language-box language-box">
                <label for="language-select" data-i18n="language">Ngôn ngữ</label>
                <select id="language-select" aria-label="Ngôn ngữ" data-i18n-aria-label="language">
                    <option value="vi" data-i18n="vietnamese">Tiếng Việt</option>
                    <option value="en" data-i18n="english">English</option>
                </select>
            </div>
        <?php endif; ?>
