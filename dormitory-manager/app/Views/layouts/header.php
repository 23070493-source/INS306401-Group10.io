<?php
$user = Auth::user();
$pageTitle = $title ?? 'Dormitory Manager';
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
                <strong><?= htmlspecialchars($user['username']) ?></strong>
                <span><?= htmlspecialchars($user['role_name']) ?></span>
            </div>

            <nav>
                <?php if ($user['role_name'] === 'Admin'): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=admin/dashboard">Dashboard</a>
                    <a href="#">Users</a>
                    <a href="#">Buildings</a>
                    <a href="#">Rooms</a>
                    <a href="#">Semesters</a>
                    <a href="#">Services</a>

                <?php elseif ($user['role_name'] === 'Manager'): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/dashboard">Dashboard</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/registrations">Room Registrations</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/contracts">Contracts</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/invoices">Invoices</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/payments">Payments</a>
                    <a href="<?= BASE_URL ?>/index.php?route=manager/maintenance">Maintenance</a>
                    <a href="#">Violations</a>

                <?php elseif ($user['role_name'] === 'Student'): ?>
                    <a href="<?= BASE_URL ?>/index.php?route=student/dashboard">Dashboard</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/rooms">Available Rooms</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/register-room">Register Room</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-registration">My Registration</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-contract">My Contract</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-invoices">My Invoices</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/maintenance">Maintenance Request</a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/index.php?route=logout">Logout</a>
            </nav>
        </aside>
    <?php endif; ?>

    <main class="<?= $user ? 'main-content' : 'auth-content' ?>">