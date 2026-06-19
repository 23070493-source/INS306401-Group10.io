<?php
$getSummary = function (string $key) use ($summary) {
    return htmlspecialchars((string) ($summary[$key] ?? 0));
};
?>

<section class="manager-dashboard role-dashboard admin-dashboard-unified">
    <div class="dashboard-hero">
        <div>
            <span class="dashboard-eyebrow" data-i18n="admin_dashboard_eyebrow">Trung tâm quản trị</span>
            <h1 data-i18n="admin_dashboard">Bảng điều khiển quản trị</h1>
            <p data-i18n="admin_dashboard_intro">
                Quản lý tài khoản, tòa nhà, phòng, học kỳ, dịch vụ và giám sát toàn bộ hệ thống.
            </p>
        </div>

        <div class="dashboard-status-card">
            <span data-i18n="admin_scope">Phạm vi quản trị</span>
            <strong data-i18n="whole_system">Toàn hệ thống</strong>
            <small data-i18n="admin_scope_note">Điều phối dữ liệu nền cho sinh viên và quản lý vận hành.</small>
        </div>
    </div>

    <div class="manager-stat-grid">
        <a class="metric-card metric-gold" href="<?= BASE_URL ?>/index.php?route=admin/users">
            <span class="metric-icon">01</span>
            <div>
                <h3 data-i18n="total_users">Tổng tài khoản</h3>
                <strong><?= $getSummary('total_users') ?></strong>
                <small data-i18n="admin_users_metric_note">Quản lý quyền truy cập toàn hệ thống</small>
            </div>
        </a>

        <a class="metric-card metric-navy" href="<?= BASE_URL ?>/index.php?route=admin/students">
            <span class="metric-icon">02</span>
            <div>
                <h3 data-i18n="total_students">Tổng sinh viên</h3>
                <strong><?= $getSummary('total_students') ?></strong>
                <small data-i18n="admin_students_metric_note">Theo dõi hồ sơ và dữ liệu sinh viên</small>
            </div>
        </a>

        <a class="metric-card metric-alert" href="<?= BASE_URL ?>/index.php?route=admin/users&role=Manager">
            <span class="metric-icon">03</span>
            <div>
                <h3 data-i18n="total_managers">Tổng quản lý</h3>
                <strong><?= $getSummary('total_managers') ?></strong>
                <small data-i18n="admin_managers_metric_note">Tài khoản phụ trách vận hành KTX</small>
            </div>
        </a>

        <a class="metric-card metric-silver" href="<?= BASE_URL ?>/index.php?route=admin/buildings">
            <span class="metric-icon">04</span>
            <div>
                <h3 data-i18n="buildings">Tòa nhà</h3>
                <strong><?= $getSummary('total_buildings') ?></strong>
                <small data-i18n="admin_buildings_metric_note">Cấu hình khu nhà ký túc xá</small>
            </div>
        </a>

        <a class="metric-card metric-danger" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
            <span class="metric-icon">05</span>
            <div>
                <h3 data-i18n="rooms">Phòng</h3>
                <strong><?= $getSummary('total_rooms') ?></strong>
                <small data-i18n="admin_rooms_metric_note">Quản lý sức chứa, loại phòng và trạng thái</small>
            </div>
        </a>
    </div>

    <div class="dashboard-workbench">
        <section class="dashboard-panel priority-panel">
            <div class="panel-heading">
                <span data-i18n="system_overview">Tổng quan hệ thống</span>
                <strong data-i18n="admin_data_queue">Dữ liệu cần chú ý</strong>
            </div>

            <a class="task-row" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
                <span class="task-count"><?= $getSummary('available_rooms') ?></span>
                <div>
                    <strong data-i18n="available_rooms">Phòng còn trống</strong>
                    <small data-i18n="available_rooms_admin_note">Kiểm tra nguồn phòng có thể tiếp nhận sinh viên.</small>
                </div>
            </a>

            <a class="task-row" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
                <span class="task-count"><?= $getSummary('maintenance_rooms') ?></span>
                <div>
                    <strong data-i18n="maintenance_rooms">Phòng bảo trì</strong>
                    <small data-i18n="maintenance_rooms_admin_note">Theo dõi phòng tạm dừng khai thác.</small>
                </div>
            </a>

            <a class="task-row" href="<?= BASE_URL ?>/index.php?route=admin/violations">
                <span class="task-count"><?= $getSummary('warning_students') ?></span>
                <div>
                    <strong data-i18n="warning_students">Sinh viên cảnh báo</strong>
                    <small data-i18n="warning_students_admin_note">Theo dõi sinh viên có điểm vi phạm vượt ngưỡng.</small>
                </div>
            </a>
        </section>

        <section class="dashboard-panel action-panel">
            <div class="panel-heading">
                <span data-i18n="quick_operations">Thao tác nhanh</span>
                <strong data-i18n="admin_tools">Công cụ quản trị</strong>
            </div>

            <div class="quick-link-grid">
                <a href="<?= BASE_URL ?>/index.php?route=admin/users" data-i18n="manage_users">Quản lý tài khoản</a>
                <a href="<?= BASE_URL ?>/index.php?route=admin/buildings" data-i18n="manage_buildings">Quản lý tòa nhà</a>
                <a href="<?= BASE_URL ?>/index.php?route=admin/rooms" data-i18n="manage_rooms">Quản lý phòng</a>
                <a href="<?= BASE_URL ?>/index.php?route=admin/semesters" data-i18n="manage_semesters">Quản lý học kỳ</a>
                <a href="<?= BASE_URL ?>/index.php?route=admin/services" data-i18n="manage_services">Quản lý dịch vụ</a>
            </div>
        </section>
    </div>

    <section class="dashboard-panel flow-panel">
        <div class="panel-heading">
            <span data-i18n="admin_flow">Luồng quản trị</span>
            <strong data-i18n="admin_data_workflow">Chuẩn hóa dữ liệu nền hệ thống</strong>
        </div>

        <div class="flow-steps">
            <div>
                <span>1</span>
                <strong data-i18n="admin_step_accounts">Tạo tài khoản và hồ sơ</strong>
            </div>
            <div>
                <span>2</span>
                <strong data-i18n="admin_step_facilities">Thiết lập tòa nhà và phòng</strong>
            </div>
            <div>
                <span>3</span>
                <strong data-i18n="admin_step_terms_services">Cấu hình học kỳ và dịch vụ</strong>
            </div>
            <div>
                <span>4</span>
                <strong data-i18n="admin_step_reports">Theo dõi nhật ký và báo cáo</strong>
            </div>
        </div>
    </section>
</section>
