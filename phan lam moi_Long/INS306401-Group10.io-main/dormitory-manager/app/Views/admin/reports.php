<?php
$crudChecklist = [
    ['roles', 'Seed / hệ thống tạo vai trò', 'Admin xem qua tài khoản và phân quyền', 'Không sửa trực tiếp để giữ ổn định RBAC', 'Không xóa', 'Admin / System'],
    ['users', 'Admin tạo tài khoản', 'Admin xem danh sách', 'Admin cập nhật thông tin / trạng thái', 'Deactivate / reset mật khẩu', 'Admin'],
    ['students', 'Admin tạo hồ sơ hoặc sinh viên đăng ký', 'Admin xem danh sách hồ sơ', 'Admin cập nhật hồ sơ sinh viên', 'Không xóa cứng khi có dữ liệu liên quan', 'Admin / Student'],
    ['buildings', 'Admin tạo tòa nhà', 'Admin xem danh sách', 'Admin cập nhật thông tin tòa nhà', 'Deactivate khi không còn sử dụng', 'Admin'],
    ['rooms', 'Admin tạo phòng', 'Admin/Manager/Student xem phòng', 'Admin cập nhật sức chứa, giá, trạng thái', 'Maintenance / inactive thay cho xóa', 'Admin'],
    ['semesters', 'Admin tạo kỳ đăng ký', 'Admin/Student xem kỳ mở', 'Admin cập nhật ngày mở/đóng', 'Đóng kỳ bằng trạng thái', 'Admin'],
    ['services', 'Admin tạo dịch vụ tính phí', 'Admin/Manager xem khi lập hóa đơn', 'Admin cập nhật đơn giá', 'Deactivate dịch vụ', 'Admin'],
    ['room_registrations', 'Student tạo đơn đăng ký', 'Student/Manager xem đơn', 'Manager cập nhật khi xử lý', 'Approve / Reject', 'Student / Manager'],
    ['contracts', 'Manager tạo khi duyệt đơn', 'Student/Manager/Admin xem', 'Manager checkout/kết thúc', 'Terminate / Expire', 'Manager'],
    ['utility_readings', 'Manager nhập chỉ số điện nước', 'Manager xem danh sách', 'Manager cập nhật trạng thái sinh hóa đơn', 'Generate invoice', 'Manager'],
    ['invoices', 'Manager tạo hóa đơn', 'Student/Manager xem', 'Hệ thống cập nhật paid/status khi duyệt thanh toán', 'Print invoice / payment process', 'Manager / Student'],
    ['invoice_details', 'Tạo kèm khi lập hóa đơn', 'Manager/Student xem trong hóa đơn in', 'Không sửa riêng sau khi phát hành', 'Theo invoice, không xóa riêng', 'Manager / System'],
    ['payments', 'Student gửi minh chứng chuyển khoản', 'Student/Manager xem', 'Manager cập nhật trạng thái duyệt', 'Approve / Reject payment', 'Student / Manager'],
    ['maintenance_requests', 'Student tạo yêu cầu sửa chữa', 'Student/Manager xem', 'Manager cập nhật trạng thái và ghi chú', 'Resolve / Cancel', 'Student / Manager'],
    ['violation_records', 'Manager tạo biên bản vi phạm', 'Student/Manager/Admin xem', 'Manager cập nhật trạng thái nếu cần', 'Record violation / warning process', 'Manager / Admin'],
    ['audit_logs', 'Hệ thống tự ghi khi có thao tác quan trọng', 'Admin xem và lọc log', 'Không cho sửa để giữ tính toàn vẹn', 'Append-only, không xóa/sửa', 'System / Admin'],
];
?>

<h1>Báo cáo</h1>
<p>Admin xem báo cáo tổng quan về phòng, hợp đồng, hóa đơn và vi phạm.</p>

<div class="cards">
    <div class="card">
        <h3>Total Students</h3>
        <strong><?= htmlspecialchars($overview['total_students']) ?></strong>
    </div>

    <div class="card">
        <h3>Active Contracts</h3>
        <strong><?= htmlspecialchars($overview['active_contracts']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Ended Contracts</h3>
        <strong><?= htmlspecialchars($overview['ended_contracts']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Pending Registrations</h3>
        <strong><?= htmlspecialchars($overview['pending_registrations']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Unpaid Invoices</h3>
        <strong><?= htmlspecialchars($overview['unpaid_invoices']) ?></strong>
    </div>

    <div class="card">
        <h3>Paid Invoices</h3>
        <strong><?= htmlspecialchars($overview['paid_invoices']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Open Maintenance</h3>
        <strong><?= htmlspecialchars($overview['open_maintenance']) ?></strong>
    </div>
</div>

<section class="report-section crud-checklist-section">
    <div class="section-heading">
        <div>
            <h2>Ma trận quản trị dữ liệu</h2>
            <p>Tóm tắt phạm vi thao tác, luồng xử lý và vai trò phụ trách theo từng bảng dữ liệu trong hệ thống.</p>
        </div>
    </div>

    <div class="table-scroll">
        <table class="crud-checklist-table">
            <thead>
            <tr>
                <th>Bảng dữ liệu</th>
                <th>Tạo mới</th>
                <th>Tra cứu</th>
                <th>Cập nhật</th>
                <th>Xử lý nghiệp vụ</th>
                <th>Vai trò phụ trách</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($crudChecklist as $item): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($item[0]) ?></strong></td>
                    <td><?= htmlspecialchars($item[1]) ?></td>
                    <td><?= htmlspecialchars($item[2]) ?></td>
                    <td><?= htmlspecialchars($item[3]) ?></td>
                    <td><?= htmlspecialchars($item[4]) ?></td>
                    <td><?= htmlspecialchars($item[5]) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<h2>Occupancy By Building</h2>

<table>
    <thead>
    <tr>
        <th>Building</th>
        <th>Total Rooms</th>
        <th>Total Capacity</th>
        <th>Active Occupancy</th>
        <th>Occupancy Rate</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($occupancyByBuilding as $item): ?>
        <?php
        $capacity = (int) $item['total_capacity'];
        $occupancy = (int) $item['active_occupancy'];
        $rate = $capacity > 0 ? round($occupancy / $capacity * 100, 1) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['building_name']) ?></td>
            <td><?= htmlspecialchars($item['total_rooms']) ?></td>
            <td><?= htmlspecialchars($capacity) ?></td>
            <td><?= htmlspecialchars($occupancy) ?></td>
            <td><?= htmlspecialchars($rate) ?>%</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Invoice Summary</h2>

<table>
    <thead>
    <tr>
        <th>Status</th>
        <th>Invoice Count</th>
        <th>Total Amount</th>
        <th>Paid Amount</th>
        <th>Remaining</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($invoiceSummary as $item): ?>
        <?php
        $total = (float) $item['total_amount'];
        $paid = (float) $item['paid_amount'];
        ?>
        <tr>
            <td><span class="badge <?= htmlspecialchars($item['status']) ?>"><?= htmlspecialchars($item['status']) ?></span></td>
            <td><?= htmlspecialchars($item['invoice_count']) ?></td>
            <td><?= number_format($total) ?> VND</td>
            <td><?= number_format($paid) ?> VND</td>
            <td><?= number_format($total - $paid) ?> VND</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Top Violation Students</h2>

<?php if (empty($topViolationStudents)): ?>
    <div class="alert success">Chưa có sinh viên vi phạm.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Student Code</th>
            <th>Full Name</th>
            <th>Violation Count</th>
            <th>Total Points</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($topViolationStudents as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['student_code']) ?></td>
                <td><?= htmlspecialchars($student['full_name']) ?></td>
                <td><?= htmlspecialchars($student['violation_count']) ?></td>
                <td><span class="badge danger"><?= htmlspecialchars($student['total_points']) ?> points</span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
