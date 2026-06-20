<?php
$total = (float) ($invoice['total_amount'] ?? 0);
$paid = (float) ($invoice['paid_amount'] ?? 0);
$remaining = max(0, $total - $paid);
?>

<section class="print-document">
    <div class="print-toolbar no-print">
        <button type="button" class="btn-print" onclick="window.print()" data-i18n="print_invoice">
            In hóa đơn
        </button>
        <a class="btn-link" href="<?= htmlspecialchars($backUrl ?? (BASE_URL . '/index.php')) ?>" data-i18n="go_back">
            Quay lại
        </a>
    </div>

    <article class="document-paper invoice-paper">
        <header class="document-header">
            <img src="<?= BASE_URL ?>/assets/img/vnu-is-logo.jpg" alt="VNU-IS">
            <div>
                <span>Hệ thống quản lý ký túc xá VNU-IS</span>
                <h1 data-i18n="print_invoice_title">Hóa đơn ký túc xá</h1>
                <p data-i18n="print_invoice_subtitle">Bản in phục vụ đối chiếu thanh toán và xác nhận công nợ.</p>
            </div>
        </header>

        <section class="document-highlight">
            <div>
                <span data-i18n="invoice_code">Mã hóa đơn</span>
                <strong><?= htmlspecialchars($invoice['invoice_code'] ?? '-') ?></strong>
            </div>
            <div>
                <span data-i18n="month">Tháng</span>
                <strong><?= htmlspecialchars($invoice['month_year'] ?? '-') ?></strong>
            </div>
            <div>
                <span data-i18n="due_date">Hạn thanh toán</span>
                <strong><?= htmlspecialchars($invoice['due_date'] ?? '-') ?></strong>
            </div>
            <div>
                <span data-i18n="status">Trạng thái</span>
                <strong><?= htmlspecialchars($invoice['status'] ?? '-') ?></strong>
            </div>
        </section>

        <section class="document-section">
            <h2 data-i18n="student_information">Thông tin sinh viên</h2>
            <div class="document-grid">
                <div>
                    <span data-i18n="student_code">Mã sinh viên</span>
                    <strong><?= htmlspecialchars($invoice['student_code'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="full_name">Họ và tên</span>
                    <strong><?= htmlspecialchars($invoice['full_name'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="contract_code">Mã hợp đồng</span>
                    <strong><?= htmlspecialchars($invoice['contract_code'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="room">Phòng</span>
                    <strong>
                        <?= htmlspecialchars($invoice['building_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($invoice['room_number'] ?? '-') ?>
                    </strong>
                </div>
            </div>
        </section>

        <section class="document-section">
            <h2 data-i18n="invoice_detail_list">Chi tiết hóa đơn</h2>

            <?php if (empty($details)): ?>
                <div class="empty-state" data-i18n="no_invoice_details">Không có dòng chi tiết hóa đơn.</div>
            <?php else: ?>
                <table class="document-table">
                    <thead>
                    <tr>
                        <th data-i18n="description">Mô tả</th>
                        <th data-i18n="quantity">Số lượng</th>
                        <th data-i18n="unit_price">Đơn giá</th>
                        <th data-i18n="amount">Số tiền</th>
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
            <h2 data-i18n="payment_summary">Tổng hợp thanh toán</h2>
            <div class="document-money-grid">
                <div>
                    <span data-i18n="total_amount">Tổng tiền</span>
                    <strong><?= number_format($total) ?> VND</strong>
                </div>
                <div>
                    <span data-i18n="paid_amount">Đã thanh toán</span>
                    <strong><?= number_format($paid) ?> VND</strong>
                </div>
                <div>
                    <span data-i18n="remaining">Còn lại</span>
                    <strong><?= number_format($remaining) ?> VND</strong>
                </div>
            </div>
        </section>

        <?php if (!empty($payments)): ?>
            <section class="document-section">
                <h2 data-i18n="payment_history">Lịch sử thanh toán</h2>
                <table class="document-table">
                    <thead>
                    <tr>
                        <th data-i18n="payment">Thanh toán</th>
                        <th data-i18n="method_label">Phương thức</th>
                        <th data-i18n="amount">Số tiền</th>
                        <th data-i18n="submitted_label">Ngày gửi</th>
                        <th data-i18n="status">Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['payment_code'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($payment['payment_method'] ?? '-') ?></td>
                            <td><?= number_format((float) ($payment['amount'] ?? 0)) ?> VND</td>
                            <td><?= htmlspecialchars($payment['payment_date'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($payment['status'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>
    </article>
</section>
