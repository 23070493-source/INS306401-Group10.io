<h1 data-i18n="admin_dashboard">Bảng điều khiển quản trị</h1>
<p data-i18n="admin_dashboard_intro">Quản lý tài khoản, tòa nhà, phòng, học kỳ, dịch vụ và giám sát toàn bộ hệ thống.</p>

<?php
$getSummary = function (string $key) use ($summary) {
    return htmlspecialchars((string)($summary[$key] ?? 0));
};
?>

<h2 data-i18n="system_overview">Tổng quan hệ thống</h2>

<div class="cards">
    <div class="card">
        <h3 data-i18n="total_users">Tổng tài khoản</h3>
        <strong><?= $getSummary('total_users') ?></strong>
    </div>

    <div class="card">
        <h3 data-i18n="total_students">Tổng sinh viên</h3>
        <strong><?= $getSummary('total_students') ?></strong>
    </div>

    <div class="card">
        <h3 data-i18n="total_managers">Tổng quản lý</h3>
        <strong><?= $getSummary('total_managers') ?></strong>
    </div>

    <div class="card">
        <h3 data-i18n="buildings">Tòa nhà</h3>
        <strong><?= $getSummary('total_buildings') ?></strong>
    </div>

    <div class="card">
        <h3 data-i18n="rooms">Phòng</h3>
        <strong><?= $getSummary('total_rooms') ?></strong>
    </div>

    <div class="card">
        <h3 data-i18n="available_rooms">Phòng còn trống</h3>
        <strong><?= $getSummary('available_rooms') ?></strong>
    </div>

    <div class="card warning">
        <h3 data-i18n="maintenance_rooms">Phòng bảo trì</h3>
        <strong><?= $getSummary('maintenance_rooms') ?></strong>
    </div>
</div>

<h2 data-i18n="operation_monitoring">Giám sát vận hành</h2>

<div class="cards">
    <div class="card">
        <h3 data-i18n="active_contracts">Hợp đồng đang hiệu lực</h3>
        <strong><?= $getSummary('active_contracts') ?></strong>
    </div>

    <div class="card warning">
        <h3 data-i18n="pending_registrations">Đơn chờ duyệt</h3>
        <strong><?= $getSummary('pending_registrations') ?></strong>
    </div>

    <div class="card danger">
        <h3 data-i18n="unpaid_invoices">Hóa đơn chưa thanh toán</h3>
        <strong><?= $getSummary('unpaid_invoices') ?></strong>
    </div>

    <div class="card warning">
        <h3 data-i18n="open_maintenance">Yêu cầu sửa chữa mở</h3>
        <strong><?= $getSummary('open_maintenance') ?></strong>
    </div>

    <div class="card danger">
        <h3 data-i18n="warning_students">Sinh viên cảnh báo</h3>
        <strong><?= $getSummary('warning_students') ?></strong>
    </div>
</div>

<h2 data-i18n="admin_quick_actions">Thao tác nhanh quản trị</h2>

<div class="cards">
    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/users">
        <h3 data-i18n="manage_users">Quản lý tài khoản</h3>
        <p data-i18n="manage_users_desc">Quản lý tài khoản Quản trị viên, Quản lý và Sinh viên.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/buildings">
        <h3 data-i18n="manage_buildings">Quản lý tòa nhà</h3>
        <p data-i18n="manage_buildings_desc">Quản lý thông tin tòa nhà KTX.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
        <h3 data-i18n="manage_rooms">Quản lý phòng</h3>
        <p data-i18n="manage_rooms_desc">Quản lý phòng, loại phòng, sức chứa và trạng thái.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/semesters">
        <h3 data-i18n="manage_semesters">Quản lý học kỳ</h3>
        <p data-i18n="manage_semesters_desc">Quản lý kỳ đăng ký KTX.</p>
    </a>

    <a class="card action-card" href="<?= BASE_URL ?>/index.php?route=admin/services">
        <h3 data-i18n="manage_services">Quản lý dịch vụ</h3>
        <p data-i18n="manage_services_desc">Quản lý các dịch vụ tính phí.</p>
    </a>
</div>
