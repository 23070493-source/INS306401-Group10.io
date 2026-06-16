<?php
$user = Auth::user();
$pageTitle = $title ?? 'Dormitory Manager';

$sidebarAvatar = null;

if ($user && !empty($user['avatar'])) {
    $sidebarAvatar = BASE_URL . '/' . ltrim($user['avatar'], '/');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="app">

    <?php if ($user): ?>
        <aside class="sidebar">
            <h2>KTX Manager</h2>

            <div class="user-box">
                <?php if ($sidebarAvatar): ?>
                    <img src="<?= htmlspecialchars($sidebarAvatar) ?>" alt="Avatar" class="sidebar-avatar">
                <?php else: ?>
                    <div class="sidebar-avatar-placeholder">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                <?php endif; ?>

                <div>
                    <strong><?= htmlspecialchars($user['username']) ?></strong>
                    <span><?= htmlspecialchars($user['role_name']) ?></span>
                </div>
            </div>

            <nav>
                <?php if ($user['role_name'] === 'Admin'): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=admin/dashboard">Dashboard</a>
                    <a href="<?= BASE_URL ?>/index.php?route=admin/users">Users</a>
                    <a href="<?= BASE_URL ?>/index.php?route=admin/buildings">Buildings</a>
                    <a href="<?= BASE_URL ?>/index.php?route=admin/rooms">Rooms</a>
                    <a href="<?= BASE_URL ?>/index.php?route=admin/semesters">Semesters</a>
                    <a href="<?= BASE_URL ?>/index.php?route=admin/services">Services</a>

                <?php elseif ($user['role_name'] === 'Manager'): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/dashboard">Dashboard</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/registrations">Room Registrations</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/contracts">Contracts</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/invoices">Invoices</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/payments">Payments</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/maintenance">Maintenance</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/violations">Violations</a>

                <?php elseif ($user['role_name'] === 'Student'): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=student/dashboard">Dashboard</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/rooms">Available Rooms</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/register-room">Register Room</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-registration">My Registration</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-contract">My Contract</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-invoices">My Invoices</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/maintenance">Maintenance Request</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/violations">My Violations</a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/index.php?route=profile">My Profile</a>
                <a href="<?= BASE_URL ?>/index.php?route=logout">Logout</a>
            </nav>
        </aside>
    <?php endif; ?>

    <main class="<?= $user ? 'main-content' : 'auth-content' ?>">