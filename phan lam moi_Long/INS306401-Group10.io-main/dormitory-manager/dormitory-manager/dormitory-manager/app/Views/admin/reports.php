<?php
$statusLabels = [
    'paid' => 'Đã thanh toán',
    'unpaid' => 'Chưa thanh toán',
    'partially_paid' => 'Thanh toán một phần',
    'overdue' => 'Quá hạn',
    'pending' => 'Chờ xử lý',
    'success' => 'Thành công',
    'rejected' => 'Từ chối',
    'cancelled' => 'Đã hủy',
];

$maxInvoiceTotal = 1;
foreach ($invoiceSummary as $item) {
    $maxInvoiceTotal = max($maxInvoiceTotal, (float) ($item['total_amount'] ?? 0));
}

$maxViolationPoints = 1;
foreach ($topViolationStudents as $student) {
    $maxViolationPoints = max($maxViolationPoints, (float) ($student['total_points'] ?? 0));
}

$crudChecklist = [
    ['roles', 'Hệ thống khởi tạo vai trò', 'Admin xem phân quyền', 'Giữ ổn định RBAC', 'Không xóa', 'Hệ thống / Admin'],
    ['users', 'Admin tạo tài khoản', 'Admin xem danh sách', 'Admin cập nhật thông tin, trạng thái', 'Khóa hoặc kích hoạt lại tài khoản', 'Admin'],
    ['students', 'Admin tạo hồ sơ hoặc sinh viên đăng ký', 'Admin xem hồ sơ', 'Admin cập nhật thông tin', 'Không xóa cứng khi có dữ liệu liên quan', 'Admin / Sinh viên'],
    ['buildings', 'Admin tạo tòa nhà', 'Admin xem danh sách', 'Admin cập nhật thông tin', 'Ngừng sử dụng khi cần', 'Admin'],
    ['rooms', 'Admin tạo phòng', 'Admin/Manager/Sinh viên xem phòng', 'Admin cập nhật sức chứa, giá, trạng thái', 'Đưa phòng về bảo trì hoặc ngừng dùng', 'Admin'],
    ['semesters', 'Admin tạo kỳ đăng ký', 'Admin/Sinh viên xem kỳ mở', 'Admin cập nhật ngày mở, ngày đóng', 'Đóng kỳ bằng trạng thái', 'Admin'],
    ['services', 'Admin tạo dịch vụ tính phí', 'Admin/Manager xem khi lập hóa đơn', 'Admin cập nhật đơn giá', 'Ngừng áp dụng dịch vụ', 'Admin'],
    ['room_registrations', 'Sinh viên tạo đơn đăng ký', 'Sinh viên/Manager xem đơn', 'Manager cập nhật khi xử lý', 'Duyệt hoặc từ chối', 'Sinh viên / Manager'],
    ['contracts', 'Manager tạo khi duyệt đơn', 'Sinh viên/Manager/Admin xem', 'Manager checkout hoặc kết thúc', 'In hợp đồng, kết thúc hợp đồng', 'Manager'],
    ['utility_readings', 'Manager nhập chỉ số điện nước', 'Manager xem danh sách', 'Manager cập nhật trạng thái sinh hóa đơn', 'Tự động sinh hóa đơn theo phòng', 'Manager'],
    ['invoices', 'Manager tạo hóa đơn', 'Sinh viên/Manager xem', 'Hệ thống cập nhật công nợ khi duyệt thanh toán', 'In hóa đơn, theo dõi thanh toán', 'Manager / Sinh viên'],
    ['invoice_details', 'Tạo kèm hóa đơn', 'Manager/Sinh viên xem trong bản in', 'Không sửa riêng sau khi phát hành', 'Theo hóa đơn gốc', 'Manager / Hệ thống'],
    ['payments', 'Sinh viên gửi minh chứng chuyển khoản', 'Sinh viên/Manager xem', 'Manager duyệt hoặc từ chối', 'Xác nhận thanh toán', 'Sinh viên / Manager'],
    ['maintenance_requests', 'Sinh viên tạo yêu cầu sửa chữa', 'Sinh viên/Manager xem', 'Manager cập nhật trạng thái và ghi chú', 'Tiếp nhận, xử lý, hoàn tất hoặc hủy', 'Sinh viên / Manager'],
    ['violation_records', 'Manager tạo biên bản vi phạm', 'Sinh viên/Manager/Admin xem', 'Manager cập nhật khi cần', 'Ghi nhận cảnh báo vi phạm', 'Manager / Admin'],
    ['audit_logs', 'Hệ thống tự ghi thao tác quan trọng', 'Admin xem nhật ký', 'Không cho sửa để giữ toàn vẹn', 'Append-only', 'Hệ thống / Admin'],
];
?>

<h1>Báo cáo</h1>
<p>Admin theo dõi tình trạng phòng, hợp đồng, hóa đơn, sửa chữa và vi phạm trong ký túc xá.</p>

<div class="cards report-overview-cards">
    <div class="card">
        <h3>Tổng sinh viên</h3>
        <strong><?= htmlspecialchars($overview['total_students']) ?></strong>
    </div>

    <div class="card">
        <h3>Hợp đồng hiệu lực</h3>
        <strong><?= htmlspecialchars($overview['active_contracts']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Đơn chờ duyệt</h3>
        <strong><?= htmlspecialchars($overview['pending_registrations']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Hóa đơn chưa thanh toán</h3>
        <strong><?= htmlspecialchars($overview['unpaid_invoices']) ?></strong>
    </div>

    <div class="card">
        <h3>Hóa đơn đã thanh toán</h3>
        <strong><?= htmlspecialchars($overview['paid_invoices']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Yêu cầu sửa chữa mở</h3>
        <strong><?= htmlspecialchars($overview['open_maintenance']) ?></strong>
    </div>
</div>

<section class="report-chart-grid">
    <article class="report-chart-card">
        <div class="section-heading compact">
            <div>
                <h2>Tỷ lệ lấp đầy theo tòa</h2>
                <p>So sánh sức chứa và số sinh viên đang có hợp đồng hiệu lực.</p>
            </div>
        </div>

        <?php if (empty($occupancyByBuilding)): ?>
            <div class="empty-state">Chưa có dữ liệu tòa nhà.</div>
        <?php else: ?>
            <div class="chart-list">
                <?php foreach ($occupancyByBuilding as $item): ?>
                    <?php
                    $capacity = (int) ($item['total_capacity'] ?? 0);
                    $occupancy = (int) ($item['active_occupancy'] ?? 0);
                    $rate = $capacity > 0 ? round($occupancy / $capacity * 100, 1) : 0;
                    ?>
                    <div class="chart-row">
                        <span><?= htmlspecialchars($item['building_name']) ?></span>
                        <div class="chart-track" aria-label="Tỷ lệ lấp đầy <?= htmlspecialchars((string) $rate) ?>%">
                            <i style="--bar: <?= htmlspecialchars((string) min(100, $rate)) ?>%;"></i>
                        </div>
                        <strong><?= htmlspecialchars((string) $rate) ?>%</strong>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <article class="report-chart-card">
        <div class="section-heading compact">
            <div>
                <h2>Tổng hợp hóa đơn</h2>
                <p>Giá trị hóa đơn theo từng trạng thái thanh toán.</p>
            </div>
        </div>

        <?php if (empty($invoiceSummary)): ?>
            <div class="empty-state">Chưa có dữ liệu hóa đơn.</div>
        <?php else: ?>
            <div class="chart-list">
                <?php foreach ($invoiceSummary as $item): ?>
                    <?php
                    $totalAmount = (float) ($item['total_amount'] ?? 0);
                    $bar = round($totalAmount / $maxInvoiceTotal * 100, 1);
                    $status = (string) ($item['status'] ?? '-');
                    ?>
                    <div class="chart-row invoice-chart-row">
                        <span><?= htmlspecialchars($statusLabels[$status] ?? $status) ?></span>
                        <div class="chart-track gold" aria-label="Tổng tiền <?= number_format($totalAmount) ?> VND">
                            <i style="--bar: <?= htmlspecialchars((string) $bar) ?>%;"></i>
                        </div>
                        <strong><?= number_format($totalAmount) ?> VND</strong>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <article class="report-chart-card">
        <div class="section-heading compact">
            <div>
                <h2>Sinh viên cần theo dõi</h2>
                <p>Top sinh viên có tổng điểm vi phạm cao nhất.</p>
            </div>
        </div>

        <?php if (empty($topViolationStudents)): ?>
            <div class="empty-state">Chưa có sinh viên vi phạm.</div>
        <?php else: ?>
            <div class="chart-list">
                <?php foreach ($topViolationStudents as $student): ?>
                    <?php
                    $points = (float) ($student['total_points'] ?? 0);
                    $bar = round($points / $maxViolationPoints * 100, 1);
                    ?>
                    <div class="chart-row danger-chart-row">
                        <span><?= htmlspecialchars($student['student_code']) ?></span>
                        <div class="chart-track danger" aria-label="<?= htmlspecialchars((string) $points) ?> điểm vi phạm">
                            <i style="--bar: <?= htmlspecialchars((string) $bar) ?>%;"></i>
                        </div>
                        <strong><?= htmlspecialchars((string) $points) ?> điểm</strong>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>

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

<section class="report-section">
    <h2>Chi tiết tỷ lệ lấp đầy</h2>
    <div class="table-scroll">
        <table>
            <thead>
            <tr>
                <th>Tòa nhà</th>
                <th>Tổng phòng</th>
                <th>Tổng sức chứa</th>
                <th>Đang ở</th>
                <th>Tỷ lệ lấp đầy</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($occupancyByBuilding as $item): ?>
                <?php
                $capacity = (int) ($item['total_capacity'] ?? 0);
                $occupancy = (int) ($item['active_occupancy'] ?? 0);
                $rate = $capacity > 0 ? round($occupancy / $capacity * 100, 1) : 0;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['building_name']) ?></td>
                    <td><?= htmlspecialchars($item['total_rooms']) ?></td>
                    <td><?= htmlspecialchars((string) $capacity) ?></td>
                    <td><?= htmlspecialchars((string) $occupancy) ?></td>
                    <td><?= htmlspecialchars((string) $rate) ?>%</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="report-section">
    <h2>Chi tiết hóa đơn</h2>
    <div class="table-scroll">
        <table>
            <thead>
            <tr>
                <th>Trạng thái</th>
                <th>Số hóa đơn</th>
                <th>Tổng tiền</th>
                <th>Đã thanh toán</th>
                <th>Còn lại</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($invoiceSummary as $item): ?>
                <?php
                $total = (float) ($item['total_amount'] ?? 0);
                $paid = (float) ($item['paid_amount'] ?? 0);
                $status = (string) ($item['status'] ?? '-');
                ?>
                <tr>
                    <td><span class="badge <?= htmlspecialchars($status) ?>"><?= htmlspecialchars($statusLabels[$status] ?? $status) ?></span></td>
                    <td><?= htmlspecialchars($item['invoice_count']) ?></td>
                    <td><?= number_format($total) ?> VND</td>
                    <td><?= number_format($paid) ?> VND</td>
                    <td><?= number_format(max(0, $total - $paid)) ?> VND</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
