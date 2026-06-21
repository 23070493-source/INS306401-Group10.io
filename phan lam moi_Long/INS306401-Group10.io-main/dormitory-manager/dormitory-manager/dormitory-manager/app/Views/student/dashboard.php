<?php
$unpaidInvoiceCount = count($unpaidInvoices ?? []);
$maintenanceCount = count($maintenanceRequests ?? []);
$violationPoints = (int) ($violationPoints ?? 0);

$violationLevel = 'Bình thường';
$violationClass = 'normal';
$violationKey = 'normal_status';

if ($violationPoints >= 15) {
    $violationLevel = 'Rất nghiêm trọng';
    $violationClass = 'critical';
    $violationKey = 'critical';
} elseif ($violationPoints >= 10) {
    $violationLevel = 'Nghiêm trọng';
    $violationClass = 'serious';
    $violationKey = 'serious';
} elseif ($violationPoints >= 5) {
    $violationLevel = 'Cảnh báo';
    $violationClass = 'warning';
    $violationKey = 'warning_label';
}

$registrationStatus = $registration['status'] ?? 'Chưa đăng ký';
$contractCode = $contract['contract_code'] ?? 'Không có hợp đồng đang hiệu lực';
$studentName = $student['full_name'] ?? 'Sinh viên';
?>

<?php if (!$student): ?>
    <div class="alert error" data-i18n="student_not_found">
        Không tìm thấy hồ sơ sinh viên.
    </div>
<?php else: ?>
    <section class="manager-dashboard role-dashboard student-dashboard-unified">
        <div class="dashboard-hero">
            <div>
                <span class="dashboard-eyebrow" data-i18n="student_portal">Cổng sinh viên</span>
                <h1 data-i18n="student_dashboard">Bảng điều khiển sinh viên</h1>
                <p data-i18n="student_dashboard_intro">
                    Theo dõi đăng ký phòng, hợp đồng, hóa đơn và các yêu cầu của bạn.
                </p>
            </div>

            <div class="dashboard-status-card">
                <span data-i18n="student_profile">Hồ sơ sinh viên</span>
                <strong><?= htmlspecialchars($studentName) ?></strong>
                <small>
                    <?= htmlspecialchars($student['student_code'] ?? '-') ?>
                    ·
                    <span><?= htmlspecialchars($student['faculty'] ?? '-') ?></span>
                </small>
            </div>
        </div>

        <div class="student-profile-strip dashboard-panel">
            <div>
                <span data-i18n="student_code">Mã sinh viên</span>
                <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
            </div>

            <div>
                <span data-i18n="gender">Giới tính</span>
                <strong><?= htmlspecialchars($student['gender'] ?? '-') ?></strong>
            </div>

            <div>
                <span data-i18n="faculty">Khoa/Viện</span>
                <strong><?= htmlspecialchars($student['faculty'] ?? '-') ?></strong>
            </div>

            <div>
                <span data-i18n="priority_type">Diện ưu tiên</span>
                <strong><?= htmlspecialchars($student['priority_type'] ?? '-') ?></strong>
            </div>
        </div>

        <div class="manager-stat-grid">
            <a class="metric-card metric-gold" href="<?= BASE_URL ?>/index.php?route=student/my-registration">
                <span class="metric-icon">01</span>
                <div>
                    <h3 data-i18n="registration_status">Trạng thái đăng ký</h3>
                    <strong class="metric-status"><?= htmlspecialchars(ucfirst((string) $registrationStatus)) ?></strong>
                    <small data-i18n="view_registration">Xem đơn đăng ký phòng của bạn</small>
                </div>
            </a>

            <a class="metric-card metric-navy" href="<?= BASE_URL ?>/index.php?route=student/my-contract">
                <span class="metric-icon">02</span>
                <div>
                    <h3 data-i18n="contract_status">Hợp đồng</h3>
                    <strong class="metric-status"><?= htmlspecialchars($contractCode) ?></strong>
                    <small data-i18n="view_contract">Theo dõi hợp đồng ký túc xá</small>
                </div>
            </a>

            <a class="metric-card metric-alert" href="<?= BASE_URL ?>/index.php?route=student/my-invoices">
                <span class="metric-icon">03</span>
                <div>
                    <h3 data-i18n="unpaid_invoice_count">Hóa đơn cần thanh toán</h3>
                    <strong><?= htmlspecialchars((string) $unpaidInvoiceCount) ?></strong>
                    <small data-i18n="view_my_invoices">Xem hóa đơn và gửi minh chứng chuyển khoản</small>
                </div>
            </a>

            <a class="metric-card metric-silver" href="<?= BASE_URL ?>/index.php?route=student/maintenance">
                <span class="metric-icon">04</span>
                <div>
                    <h3 data-i18n="maintenance_requests">Yêu cầu sửa chữa</h3>
                    <strong><?= htmlspecialchars((string) $maintenanceCount) ?></strong>
                    <small data-i18n="submit_maintenance_request">Gửi và theo dõi yêu cầu sửa chữa</small>
                </div>
            </a>

            <a class="metric-card metric-danger" href="<?= BASE_URL ?>/index.php?route=student/violations">
                <span class="metric-icon">05</span>
                <div>
                    <h3 data-i18n="violation_points">Điểm vi phạm</h3>
                    <strong><?= htmlspecialchars((string) $violationPoints) ?></strong>
                    <small class="badge <?= htmlspecialchars($violationClass) ?>" data-i18n="<?= htmlspecialchars($violationKey) ?>">
                        <?= htmlspecialchars($violationLevel) ?>
                    </small>
                </div>
            </a>
        </div>

        <div class="dashboard-workbench">
            <section class="dashboard-panel priority-panel">
                <div class="panel-heading">
                    <span data-i18n="student_priority_tasks">Theo dõi cá nhân</span>
                    <strong data-i18n="current_room">Phòng hiện tại</strong>
                </div>

                <?php if ($contract): ?>
                    <div class="student-room-summary">
                        <div>
                            <span data-i18n="building">Tòa nhà</span>
                            <strong><?= htmlspecialchars($contract['building_name'] ?? '-') ?></strong>
                        </div>

                        <div>
                            <span data-i18n="room">Phòng</span>
                            <strong><?= htmlspecialchars($contract['room_number'] ?? '-') ?></strong>
                        </div>

                        <div>
                            <span data-i18n="start_date">Ngày bắt đầu</span>
                            <strong><?= htmlspecialchars($contract['start_date'] ?? '-') ?></strong>
                        </div>

                        <div>
                            <span data-i18n="end_date">Ngày kết thúc</span>
                            <strong><?= htmlspecialchars($contract['end_date'] ?? '-') ?></strong>
                        </div>

                        <div>
                            <span data-i18n="monthly_price">Giá hàng tháng</span>
                            <strong><?= number_format((float) ($contract['monthly_price'] ?? 0)) ?> VND</strong>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="dashboard-empty-state" data-i18n="current_room_empty">
                        Chưa có hợp đồng phòng đang hiệu lực.
                    </div>
                <?php endif; ?>

                <div class="panel-heading compact-heading">
                    <span data-i18n="recent_activity">Hoạt động gần đây</span>
                    <strong data-i18n="recent_maintenance_requests">Yêu cầu sửa chữa mới</strong>
                </div>

                <?php if (empty($maintenanceRequests)): ?>
                    <div class="dashboard-empty-state" data-i18n="no_maintenance_requests">
                        Bạn chưa có yêu cầu sửa chữa nào.
                    </div>
                <?php else: ?>
                    <?php foreach ($maintenanceRequests as $request): ?>
                        <?php
                        $requestTitle = $request['title'] ?? $request['issue_title'] ?? 'Yêu cầu sửa chữa';
                        $requestStatus = $request['status'] ?? '-';
                        ?>
                        <a class="task-row" href="<?= BASE_URL ?>/index.php?route=student/maintenance">
                            <span class="task-count"><?= htmlspecialchars(substr((string) $request['id'], -2)) ?></span>
                            <div>
                                <strong><?= htmlspecialchars($requestTitle) ?></strong>
                                <small>
                                    <?= htmlspecialchars($request['request_date'] ?? '-') ?>
                                    ·
                                    <span class="badge <?= htmlspecialchars($requestStatus) ?>">
                                        <?= htmlspecialchars(ucfirst((string) $requestStatus)) ?>
                                    </span>
                                </small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <section class="dashboard-panel action-panel">
                <div class="panel-heading">
                    <span data-i18n="quick_operations">Thao tác nhanh</span>
                    <strong data-i18n="student_tools">Công cụ sinh viên</strong>
                </div>

                <div class="quick-link-grid">
                    <a href="<?= BASE_URL ?>/index.php?route=student/rooms" data-i18n="find_available_rooms">Tìm phòng còn trống</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/register-room" data-i18n="submit_room_registration">Đăng ký phòng</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-invoices" data-i18n="view_my_invoices">Xem hóa đơn và gửi minh chứng chuyển khoản</a>
                    <a href="<?= BASE_URL ?>/index.php?route=student/maintenance" data-i18n="submit_maintenance_request">Gửi và theo dõi yêu cầu sửa chữa</a>
                </div>
            </section>
        </div>

        <section class="dashboard-panel flow-panel">
            <div class="panel-heading">
                <span data-i18n="student_flow">Luồng sinh viên</span>
                <strong data-i18n="student_dormitory_workflow">Quy trình sử dụng ký túc xá</strong>
            </div>

            <div class="flow-steps">
                <div>
                    <span>1</span>
                    <strong data-i18n="student_step_register">Đăng ký phòng</strong>
                </div>
                <div>
                    <span>2</span>
                    <strong data-i18n="student_step_contract">Theo dõi hợp đồng</strong>
                </div>
                <div>
                    <span>3</span>
                    <strong data-i18n="student_step_payment">Gửi minh chứng thanh toán</strong>
                </div>
                <div>
                    <span>4</span>
                    <strong data-i18n="student_step_support">Gửi sửa chữa và theo dõi vi phạm</strong>
                </div>
            </div>
        </section>
    </section>
<?php endif; ?>
