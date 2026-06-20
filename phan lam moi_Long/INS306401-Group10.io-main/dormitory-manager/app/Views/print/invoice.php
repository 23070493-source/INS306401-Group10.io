<?php
$statusLabels = [
    'paid' => 'Đã thanh toán',
    'unpaid' => 'Chưa thanh toán',
    'partially_paid' => 'Thanh toán một phần',
    'overdue' => 'Quá hạn',
    'cancelled' => 'Đã hủy',
];

$total = (float) ($invoice['total_amount'] ?? 0);
$paid = (float) ($invoice['paid_amount'] ?? 0);
$remaining = max(0, $total - $paid);
$status = (string) ($invoice['status'] ?? '-');
?>

<section class="print-document a4-document">
    <div class="print-toolbar no-print">
        <button type="button" class="btn-print" onclick="window.print()">
            In / Lưu PDF hóa đơn
        </button>
        <a class="btn-link" href="<?= htmlspecialchars($backUrl ?? (BASE_URL . '/index.php')) ?>">
            Quay lại
        </a>
    </div>

    <article class="document-paper a4-paper invoice-paper">
        <header class="document-header">
            <img src="<?= BASE_URL ?>/assets/img/vnu-is-logo.jpg" alt="VNU-IS">
            <div>
                <span>Hệ thống quản lý ký túc xá VNU-IS</span>
                <h1>Hóa đơn ký túc xá</h1>
                <p>Bản in phục vụ đối chiếu thanh toán và xác nhận công nợ.</p>
            </div>
        </header>

        <section class="document-highlight">
            <div>
                <span>Mã hóa đơn</span>
                <strong><?= htmlspecialchars($invoice['invoice_code'] ?? '-') ?></strong>
            </div>
            <div>
                <span>Tháng</span>
                <strong><?= htmlspecialchars($invoice['month_year'] ?? $invoice['invoice_month'] ?? '-') ?></strong>
            </div>
            <div>
                <span>Hạn thanh toán</span>
                <strong><?= htmlspecialchars($invoice['due_date'] ?? '-') ?></strong>
            </div>
            <div>
                <span>Trạng thái</span>
                <strong><?= htmlspecialchars($statusLabels[$status] ?? $status) ?></strong>
            </div>
        </section>

        <section class="document-section">
            <h2>Thông tin sinh viên</h2>
            <div class="document-grid">
                <div>
                    <span>Mã sinh viên</span>
                    <strong><?= htmlspecialchars($invoice['student_code'] ?? '-') ?></strong>
                </div>
                <div>
                    <span>Họ và tên</span>
                    <strong><?= htmlspecialchars($invoice['full_name'] ?? '-') ?></strong>
                </div>
                <div>
                    <span>Mã hợp đồng</span>
                    <strong><?= htmlspecialchars($invoice['contract_code'] ?? '-') ?></strong>
                </div>
                <div>
                    <span>Phòng</span>
                    <strong>
                        <?= htmlspecialchars($invoice['building_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($invoice['room_number'] ?? '-') ?>
                    </strong>
                </div>
            </div>
        </section>

        <section class="document-section">
            <h2>Chi tiết hóa đơn</h2>

            <?php if (empty($details)): ?>
                <div class="empty-state">Không có dòng chi tiết hóa đơn.</div>
            <?php else: ?>
                <table class="document-table">
                    <thead>
                    <tr>
                        <th>Mô tả</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($details as $detail): ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['description'] ?? $detail['service_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars((string) ($detail['quantity'] ?? 1)) ?></td>
                            <td><?= number_format((float) ($detail['unit_price'] ?? 0)) ?> VND</td>
                            <td><?= number_format((float) ($detail['amount'] ?? 0)) ?> VND</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="document-section">
            <h2>Tổng hợp thanh toán</h2>
            <div class="document-money-grid">
                <div>
                    <span>Tổng tiền</span>
                    <strong><?= number_format($total) ?> VND</strong>
                </div>
                <div>
                    <span>Đã thanh toán</span>
                    <strong><?= number_format($paid) ?> VND</strong>
                </div>
                <div>
                    <span>Còn lại</span>
                    <strong><?= number_format($remaining) ?> VND</strong>
                </div>
            </div>
        </section>

        <?php if (!empty($payments)): ?>
            <section class="document-section">
                <h2>Lịch sử thanh toán</h2>
                <table class="document-table">
                    <thead>
                    <tr>
                        <th>Phiếu thanh toán</th>
                        <th>Phương thức</th>
                        <th>Số tiền</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <?php $paymentStatus = (string) ($payment['status'] ?? '-'); ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['payment_code'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($payment['payment_method'] ?? '-') ?></td>
                            <td><?= number_format((float) ($payment['amount'] ?? 0)) ?> VND</td>
                            <td><?= htmlspecialchars($payment['payment_date'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($statusLabels[$paymentStatus] ?? $paymentStatus) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>

        <section class="signature-grid invoice-signature-grid">
            <div>
                <strong>Người nộp</strong>
                <span>Ký và ghi rõ họ tên</span>
            </div>
            <div>
                <strong>Quản lý ký túc xá</strong>
                <span>Ký và ghi rõ họ tên</span>
            </div>
        </section>
    </article>
</section>
