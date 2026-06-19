<section class="manager-dashboard">
    <div class="dashboard-hero">
        <div>
            <span class="dashboard-eyebrow" data-i18n="manager_dashboard_eyebrow">Trung tâm vận hành</span>
            <h1 data-i18n="manager_dashboard">Bảng điều khiển quản lý</h1>
            <p data-i18n="manager_dashboard_intro">
                Quản lý đăng ký phòng, hợp đồng, hóa đơn, sự cố và vi phạm.
            </p>
        </div>

        <div class="dashboard-status-card">
            <span data-i18n="system_status">Trạng thái hệ thống</span>
            <strong data-i18n="ready_for_operations">Đang sẵn sàng vận hành</strong>
            <small data-i18n="dashboard_data_note">Dữ liệu đang lấy trực tiếp từ cơ sở dữ liệu demo.</small>
        </div>
    </div>

    <div class="manager-stat-grid">
        <a class="metric-card metric-gold" href="<?= BASE_URL ?>/index.php?route=manager/registrations">
            <span class="metric-icon">01</span>
            <div>
                <h3 data-i18n="pending_registrations">Đơn chờ duyệt</h3>
                <strong><?= htmlspecialchars($summary['pending_registrations']) ?></strong>
                <small data-i18n="review_room_requests">Xem và duyệt đơn đăng ký phòng</small>
            </div>
        </a>

        <a class="metric-card metric-navy" href="<?= BASE_URL ?>/index.php?route=manager/contracts">
            <span class="metric-icon">02</span>
            <div>
                <h3 data-i18n="active_contracts_effective">Hợp đồng đang hiệu lực</h3>
                <strong><?= htmlspecialchars($summary['active_contracts']) ?></strong>
                <small data-i18n="review_active_contracts">Theo dõi hợp đồng đang ở ký túc xá</small>
            </div>
        </a>

        <a class="metric-card metric-alert" href="<?= BASE_URL ?>/index.php?route=manager/invoices">
            <span class="metric-icon">03</span>
            <div>
                <h3 data-i18n="manager_dashboard_unpaid_overdue">Hóa đơn chưa thanh toán / quá hạn</h3>
                <strong><?= htmlspecialchars($summary['unpaid_invoices']) ?></strong>
                <small data-i18n="verify_invoices_payments">Kiểm tra hóa đơn và thanh toán</small>
            </div>
        </a>

        <a class="metric-card metric-silver" href="<?= BASE_URL ?>/index.php?route=manager/maintenance">
            <span class="metric-icon">04</span>
            <div>
                <h3 data-i18n="manager_dashboard_open_maintenance">Yêu cầu sửa chữa đang mở</h3>
                <strong><?= htmlspecialchars($summary['open_maintenance']) ?></strong>
                <small data-i18n="coordinate_repairs">Điều phối xử lý sự cố phòng ở</small>
            </div>
        </a>

        <a class="metric-card metric-danger" href="<?= BASE_URL ?>/index.php?route=manager/violations">
            <span class="metric-icon">05</span>
            <div>
                <h3 data-i18n="manager_dashboard_violation_warnings">Cảnh báo vi phạm</h3>
                <strong><?= htmlspecialchars($summary['warning_students']) ?></strong>
                <small data-i18n="review_violation_records">Theo dõi sinh viên có điểm vi phạm</small>
            </div>
        </a>
    </div>

    <div class="dashboard-workbench">
        <section class="dashboard-panel priority-panel">
            <div class="panel-heading">
                <span data-i18n="priority_tasks">Công việc ưu tiên</span>
                <strong data-i18n="today_queue">Hàng đợi hôm nay</strong>
            </div>

            <a class="task-row" href="<?= BASE_URL ?>/index.php?route=manager/registrations">
                <span class="task-count"><?= htmlspecialchars($summary['pending_registrations']) ?></span>
                <div>
                    <strong data-i18n="pending_registration_review">Duyệt đơn đăng ký phòng</strong>
                    <small data-i18n="registrations_subtitle">Ưu tiên xử lý đơn đang chờ để sinh viên nhận phòng đúng hạn.</small>
                </div>
            </a>

            <a class="task-row" href="<?= BASE_URL ?>/index.php?route=manager/payments">
                <span class="task-count"><?= htmlspecialchars($summary['unpaid_invoices']) ?></span>
                <div>
                    <strong data-i18n="unpaid_invoice_followup">Đối soát hóa đơn chưa thanh toán</strong>
                    <small data-i18n="unpaid_invoice_subtitle">Kiểm tra khoản chuyển khoản và cập nhật trạng thái hóa đơn.</small>
                </div>
            </a>

            <a class="task-row" href="<?= BASE_URL ?>/index.php?route=manager/maintenance">
                <span class="task-count"><?= htmlspecialchars($summary['open_maintenance']) ?></span>
                <div>
                    <strong data-i18n="open_maintenance_followup">Theo dõi yêu cầu sửa chữa</strong>
                    <small data-i18n="open_maintenance_subtitle">Phân loại mức ưu tiên và cập nhật tiến độ xử lý.</small>
                </div>
            </a>
        </section>

        <section class="dashboard-panel action-panel">
            <div class="panel-heading">
                <span data-i18n="quick_operations">Thao tác nhanh</span>
                <strong data-i18n="manager_tools">Công cụ quản lý</strong>
            </div>

            <div class="quick-link-grid">
                <a href="<?= BASE_URL ?>/index.php?route=manager/invoice-create" data-i18n="create_invoice">Tạo hóa đơn</a>
                <a href="<?= BASE_URL ?>/index.php?route=manager/payments" data-i18n="verify_payments">Xác nhận thanh toán</a>
                <a href="<?= BASE_URL ?>/index.php?route=manager/utility-readings" data-i18n="record_utility_readings">Nhập chỉ số điện nước</a>
                <a href="<?= BASE_URL ?>/index.php?route=manager/violations" data-i18n="create_violation_record">Tạo biên bản vi phạm</a>
            </div>
        </section>
    </div>

    <section class="dashboard-panel flow-panel">
        <div class="panel-heading">
            <span data-i18n="operational_flow">Luồng vận hành</span>
            <strong data-i18n="weekly_flow">Theo dõi quy trình ký túc xá</strong>
        </div>

        <div class="flow-steps">
            <div>
                <span>1</span>
                <strong data-i18n="step_room_registration">Duyệt đăng ký phòng</strong>
            </div>
            <div>
                <span>2</span>
                <strong data-i18n="step_contract_invoice">Tạo hợp đồng và hóa đơn</strong>
            </div>
            <div>
                <span>3</span>
                <strong data-i18n="step_payment_confirm">Xác nhận thanh toán</strong>
            </div>
            <div>
                <span>4</span>
                <strong data-i18n="step_maintenance_violation">Theo dõi sửa chữa và vi phạm</strong>
            </div>
        </div>
    </section>
</section>
