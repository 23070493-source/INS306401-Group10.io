<h1>Admin Dashboard</h1>
<p>Quản lý tài khoản, tòa nhà, phòng, học kỳ, dịch vụ và giám sát toàn bộ hệ thống.</p>

<?php
$getSummary = function (string $key) use ($summary) {
    return htmlspecialchars((string)($summary[$key] ?? 0));
};
?>

<h2>System Overview</h2>

<div class="cards">
    <div class="card">
        <h3>Total Users</h3>
        <strong><?= $getSummary('total_users') ?></strong>
    </div>

    <div class="card">
        <h3>Total Students</h3>
        <strong><?= $getSummary('total_students') ?></strong>
    </div>

    <div class="card">
        <h3>Total Managers</h3>
        <strong><?= $getSummary('total_managers') ?></strong>
    </div>

    <div class="card">
        <h3>Buildings</h3>
        <strong><?= $getSummary('total_buildings') ?></strong>
    </div>

    <div class="card">
        <h3>Rooms</h3>
        <strong><?= $getSummary('total_rooms') ?></strong>
    </div>

    <div class="card">
        <h3>Available Rooms</h3>
        <strong><?= $getSummary('available_rooms') ?></strong>
    </div>

    <div class="card warning">
        <h3>Maintenance Rooms</h3>
        <strong><?= $getSummary('maintenance_rooms') ?></strong>
    </div>
</div>

<h2>Operation Monitoring</h2>

<div class="cards">
    <div class="card">
        <h3>Active Contracts</h3>
        <strong><?= $getSummary('active_contracts') ?></strong>
    </div>

    <div class="card warning">
        <h3>Pending Registrations</h3>
        <strong><?= $getSummary('pending_registrations') ?></strong>
    </div>

    <div class="card danger">
        <h3>Unpaid Invoices</h3>
        <strong><?= $getSummary('unpaid_invoices') ?></strong>
    </div>

    <div class="card warning">
        <h3>Open Maintenance</h3>
        <strong><?= $getSummary('open_maintenance') ?></strong>
    </div>

    <div class="card danger">
        <h3>Warning Students</h3>
        <strong><?= $getSummary('warning_students') ?></strong>
    </div>
</div>

<h2>Admin Quick Actions</h2>

<div class="cards">
    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/users">
        <h3>Manage Users</h3>
        <p>Quản lý tài khoản Admin, Manager và Student.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/buildings">
        <h3>Manage Buildings</h3>
        <p>Quản lý thông tin tòa nhà KTX.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
        <h3>Manage Rooms</h3>
        <p>Quản lý phòng, loại phòng, sức chứa và trạng thái.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/semesters">
        <h3>Manage Semesters</h3>
        <p>Quản lý kỳ đăng ký KTX.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/services">
        <h3>Manage Services</h3>
        <p>Quản lý các dịch vụ tính phí.</p>
    </a>
</div>