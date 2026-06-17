<?php
$user = Auth::user();
$pageTitle = $title ?? 'Dormitory Manager';

$currentRoute = $_GET['route'] ?? 'home';

$roleName = $user['role_name'] ?? 'Guest';

$roleClass = 'guest';
$roleCss = 'auth.css';
$roleLabel = 'Guest Portal';

if ($roleName === 'Admin') {
    $roleClass = 'admin';
    $roleCss = 'admin.css';
    $roleLabel = 'System Control Center';
} elseif ($roleName === 'Manager') {
    $roleClass = 'manager';
    $roleCss = 'manager.css';
    $roleLabel = 'Operation Dashboard';
} elseif ($roleName === 'Student') {
    $roleClass = 'student';
    $roleCss = 'student.css';
    $roleLabel = 'Student Dormitory Portal';
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css">

    <!-- Role-based theme -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/<?= htmlspecialchars($roleCss) ?>">
</head>

<body class="role-<?= htmlspecialchars($roleClass) ?>">

<div class="<?= $user ? 'app app-dashboard' : 'app app-auth' ?>">

    <?php if ($user): ?>
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">K</div>

                <div>
                    <h2>KTX Manager</h2>
                    <p><?= htmlspecialchars($roleLabel) ?></p>
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
                    <span class="role-pill role-pill-<?= htmlspecialchars($roleClass) ?>">
                        <?= htmlspecialchars($user['role_name']) ?>
                    </span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php if ($user['role_name'] === 'Admin'): ?>
                    <a class="<?= $isActive('admin/dashboard') ?>" href="<?= BASE_URL ?>/index.php?route=admin/dashboard">
                        <span class="nav-icon">▦</span>
                        <span>Dashboard</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/users']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/users">
                        <span class="nav-icon">👥</span>
                        <span>Users</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/students']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/students">
                        <span class="nav-icon">🎓</span>
                        <span>Students</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/buildings']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings">
                        <span class="nav-icon">🏢</span>
                        <span>Buildings</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/rooms']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
                        <span class="nav-icon">🚪</span>
                        <span>Rooms</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/semesters']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters">
                        <span class="nav-icon">📅</span>
                        <span>Semesters</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/services']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/services">
                        <span class="nav-icon">🧾</span>
                        <span>Services</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/audit-logs']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/audit-logs">
                        <span class="nav-icon">🛡</span>
                        <span>Audit Logs</span>
                    </a>

                    <a class="<?= $isActiveGroup(['admin/reports']) ?>" href="<?= BASE_URL ?>/index.php?route=admin/reports">
                        <span class="nav-icon">📊</span>
                        <span>Reports</span>
                    </a>

                <?php elseif ($user['role_name'] === 'Manager'): ?>
                    <a class="<?= $isActive('manager/dashboard') ?>" href="<?= BASE_URL ?>/index.php?route=manager/dashboard">
                        <span class="nav-icon">▦</span>
                        <span>Dashboard</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/registrations', 'manager/registration-detail']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/registrations">
                        <span class="nav-icon">📝</span>
                        <span>Room Registrations</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/contracts']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts">
                        <span class="nav-icon">📄</span>
                        <span>Contracts</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/invoices', 'manager/invoice-create']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices">
                        <span class="nav-icon">🧾</span>
                        <span>Invoices</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/utility-readings']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/utility-readings">
                        <span class="nav-icon">⚡</span>
                        <span>Utility Readings</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/payments']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments">
                        <span class="nav-icon">💳</span>
                        <span>Payments</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/maintenance']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance">
                        <span class="nav-icon">🛠</span>
                        <span>Maintenance</span>
                    </a>

                    <a class="<?= $isActiveGroup(['manager/violations']) ?>" href="<?= BASE_URL ?>/index.php?route=manager/violations">
                        <span class="nav-icon">⚠</span>
                        <span>Violations</span>
                    </a>

                <?php elseif ($user['role_name'] === 'Student'): ?>
                    <a class="<?= $isActive('student/dashboard') ?>" href="<?= BASE_URL ?>/index.php?route=student/dashboard">
                        <span class="nav-icon">▦</span>
                        <span>Dashboard</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/rooms']) ?>" href="<?= BASE_URL ?>/index.php?route=student/rooms">
                        <span class="nav-icon">🏠</span>
                        <span>Available Rooms</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/register-room']) ?>" href="<?= BASE_URL ?>/index.php?route=student/register-room">
                        <span class="nav-icon">📝</span>
                        <span>Register Room</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/my-registration']) ?>" href="<?= BASE_URL ?>/index.php?route=student/my-registration">
                        <span class="nav-icon">📌</span>
                        <span>My Registration</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/my-contract']) ?>" href="<?= BASE_URL ?>/index.php?route=student/my-contract">
                        <span class="nav-icon">📄</span>
                        <span>My Contract</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/my-invoices', 'student/payment-submit', 'student/pay-invoice']) ?>" href="<?= BASE_URL ?>/index.php?route=student/my-invoices">
                        <span class="nav-icon">🧾</span>
                        <span>My Invoices</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/maintenance']) ?>" href="<?= BASE_URL ?>/index.php?route=student/maintenance">
                        <span class="nav-icon">🛠</span>
                        <span>Maintenance Request</span>
                    </a>

                    <a class="<?= $isActiveGroup(['student/violations']) ?>" href="<?= BASE_URL ?>/index.php?route=student/violations">
                        <span class="nav-icon">⚠</span>
                        <span>My Violations</span>
                    </a>
                <?php endif; ?>

                <div class="sidebar-divider"></div>

                <a class="<?= $isActive('profile') ?>" href="<?= BASE_URL ?>/index.php?route=profile">
                    <span class="nav-icon">👤</span>
                    <span>My Profile</span>
                </a>

                <a class="logout-link" href="<?= BASE_URL ?>/index.php?route=logout">
                    <span class="nav-icon">↪</span>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>
    <?php endif; ?>

    <main class="<?= $user ? 'main-content' : 'auth-content' ?>">