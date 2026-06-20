<?php
$totalInvoices = count($invoices ?? []);

$totalAmount = 0;
$totalPaid = 0;
$totalPending = 0;
$totalRemaining = 0;

$paidCount = 0;
$unpaidCount = 0;
$pendingCount = 0;

$invoiceStatusLabels = [
    'paid' => 'Đã thanh toán',
    'unpaid' => 'Chưa thanh toán',
    'partially_paid' => 'Thanh toán một phần',
    'overdue' => 'Quá hạn',
    'cancelled' => 'Đã hủy',
];
$labelStatus = static function (?string $value) use ($invoiceStatusLabels): string {
    $key = strtolower(trim((string) $value));
    return $invoiceStatusLabels[$key] ?? ($value ?: '-');
};

foreach ($invoices ?? [] as $invoice) {
    $invoiceTotal = (float) ($invoice['total_amount'] ?? 0);
    $invoicePaid = (float) ($invoice['paid_amount'] ?? 0);
    $invoicePending = (float) ($invoice['pending_amount'] ?? 0);
    $invoiceRemaining = max(0, $invoiceTotal - $invoicePaid - $invoicePending);

    $totalAmount += $invoiceTotal;
    $totalPaid += $invoicePaid;
    $totalPending += $invoicePending;
    $totalRemaining += $invoiceRemaining;

    $status = $invoice['status'] ?? '';

    if ($status === 'paid') {
        $paidCount++;
    } elseif ($invoicePending > 0) {
        $pendingCount++;
    } else {
        $unpaidCount++;
    }
}
?>

<h1>Hóa đơn của tôi</h1>

<?php if (!$student): ?>
    <div class="alert error">
        Không tìm thấy hồ sơ sinh viên.
    </div>

<?php elseif (empty($invoices)): ?>
    <section class="student-invoice-empty">
        <div class="empty-state">
            Chưa có hóa đơn nào.
        </div>
    </section>

<?php else: ?>

    <section class="student-invoice-hero">
        <div>
            <span class="student-page-label">Tổng quan hóa đơn</span>
            <h2><?= htmlspecialchars($student['full_name'] ?? '-') ?></h2>
        </div>

        <div class="student-invoice-student-info">
            <div>
                <span>Mã sinh viên</span>
                <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
            </div>

            <div>
                <span>Khoa/Viện</span>
                <strong><?= htmlspecialchars($student['faculty'] ?? '-') ?></strong>
            </div>
        </div>
    </section>

    <section class="student-invoice-summary-grid">
        <div class="student-invoice-summary-card">
            <span>Tổng số hóa đơn</span>
            <strong><?= htmlspecialchars((string) $totalInvoices) ?></strong>
        </div>

        <div class="student-invoice-summary-card">
            <span>Tổng tiền</span>
            <strong><?= number_format($totalAmount) ?> VND</strong>
        </div>

        <div class="student-invoice-summary-card">
            <span>Đã thanh toán</span>
            <strong><?= number_format($totalPaid) ?> VND</strong>
        </div>

        <div class="student-invoice-summary-card">
            <span>Còn phải trả</span>
            <strong><?= number_format($totalRemaining) ?> VND</strong>
        </div>
    </section>

    <section class="student-invoice-status-grid">
        <div class="student-invoice-status-card">
            <span>Đã thanh toán</span>
            <strong><?= htmlspecialchars((string) $paidCount) ?></strong>
        </div>

        <div class="student-invoice-status-card">
            <span>Chờ xác nhận</span>
            <strong><?= htmlspecialchars((string) $pendingCount) ?></strong>
        </div>

        <div class="student-invoice-status-card">
            <span>Chưa thanh toán</span>
            <strong><?= htmlspecialchars((string) $unpaidCount) ?></strong>
        </div>

        <div class="student-invoice-status-card">
            <span>Số tiền chờ xác nhận</span>
            <strong><?= number_format($totalPending) ?> VND</strong>
        </div>
    </section>

    <section class="student-invoice-card-list">
        <?php foreach ($invoices as $invoice): ?>
            <?php
            $total = (float) ($invoice['total_amount'] ?? 0);
            $paid = (float) ($invoice['paid_amount'] ?? 0);
            $pending = (float) ($invoice['pending_amount'] ?? 0);
            $remaining = max(0, $total - $paid - $pending);

            $invoiceStatus = $invoice['status'] ?? 'unpaid';
            $month = $invoice['month_year'] ?? $invoice['invoice_month'] ?? '-';
            ?>

            <article class="student-invoice-card">
                <div class="student-invoice-card-header">
                    <div>
                        <span>Hóa đơn</span>
                        <h2><?= htmlspecialchars($invoice['invoice_code'] ?? '-') ?></h2>
                    </div>

                    <span class="badge <?= htmlspecialchars($invoiceStatus) ?>">
                        <?= htmlspecialchars($labelStatus($invoiceStatus)) ?>
                    </span>
                </div>

                <div class="student-invoice-meta-grid">
                    <div>
                        <span>Hợp đồng</span>
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

                    <div>
                        <span>Tháng</span>
                        <strong><?= htmlspecialchars($month) ?></strong>
                    </div>

                    <div>
                        <span>Hạn thanh toán</span>
                        <strong><?= htmlspecialchars($invoice['due_date'] ?? '-') ?></strong>
                    </div>
                </div>

                <div class="student-invoice-money-grid">
                    <div>
                        <span>Tổng tiền</span>
                        <strong><?= number_format($total) ?> VND</strong>
                    </div>

                    <div>
                        <span>Đã thanh toán</span>
                        <strong><?= number_format($paid) ?> VND</strong>
                    </div>

                    <div>
                        <span>Chờ xác nhận</span>
                        <strong><?= number_format($pending) ?> VND</strong>
                    </div>

                    <div>
                        <span>Còn lại</span>
                        <strong><?= number_format($remaining) ?> VND</strong>
                    </div>
                </div>

                <div class="student-invoice-action">
                    <?php if ($invoiceStatus === 'paid' || $remaining <= 0): ?>
                        <span class="badge paid">Đã thanh toán</span>

                    <?php elseif ($pending > 0): ?>
                        <span class="badge pending">Chờ xác nhận</span>

                    <?php else: ?>
                        <a
                            class="student-primary-link"
                            href="<?= BASE_URL ?>/index.php?route=student/payment-submit&invoice_id=<?= htmlspecialchars((string) ($invoice['id'] ?? '')) ?>"
                        >
                            Gửi minh chứng chuyển khoản
                        </a>
                    <?php endif; ?>

                    <a
                        class="student-primary-link no-print"
                        href="<?= BASE_URL ?>/index.php?route=student/invoice-print&invoice_id=<?= htmlspecialchars((string) ($invoice['id'] ?? '')) ?>"
                        data-i18n="print_invoice"
                    >
                        In hóa đơn
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Bảng hóa đơn</h2>
            </div>
        </div>

        <div class="student-table-scroll">
            <table>
                <thead>
                <tr>
                    <th>Mã hóa đơn</th>
                    <th>Hợp đồng</th>
                    <th>Phòng</th>
                    <th>Tháng</th>
                    <th>Hạn thanh toán</th>
                    <th>Tổng tiền</th>
                    <th>Đã thanh toán</th>
                    <th>Chờ xác nhận</th>
                    <th>Còn lại</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <?php
                    $total = (float) ($invoice['total_amount'] ?? 0);
                    $paid = (float) ($invoice['paid_amount'] ?? 0);
                    $pending = (float) ($invoice['pending_amount'] ?? 0);
                    $remaining = max(0, $total - $paid - $pending);

                    $invoiceStatus = $invoice['status'] ?? 'unpaid';
                    $month = $invoice['month_year'] ?? $invoice['invoice_month'] ?? '-';
                    ?>

                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($invoice['invoice_code'] ?? '-') ?></strong>
                        </td>

                        <td><?= htmlspecialchars($invoice['contract_code'] ?? '-') ?></td>

                        <td>
                            <?= htmlspecialchars($invoice['building_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($invoice['room_number'] ?? '-') ?>
                        </td>

                        <td><?= htmlspecialchars($month) ?></td>

                        <td><?= htmlspecialchars($invoice['due_date'] ?? '-') ?></td>

                        <td><?= number_format($total) ?> VND</td>

                        <td><?= number_format($paid) ?> VND</td>

                        <td><?= number_format($pending) ?> VND</td>

                        <td><?= number_format($remaining) ?> VND</td>

                        <td>
                            <span class="badge <?= htmlspecialchars($invoiceStatus) ?>">
                                <?= htmlspecialchars($labelStatus($invoiceStatus)) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($invoiceStatus === 'paid' || $remaining <= 0): ?>
                                <span class="badge paid">Đã thanh toán</span>

                            <?php elseif ($pending > 0): ?>
                                <span class="badge pending">Chờ xác nhận</span>

                            <?php else: ?>
                                <a
                                    class="btn-link"
                                    href="<?= BASE_URL ?>/index.php?route=student/payment-submit&invoice_id=<?= htmlspecialchars((string) ($invoice['id'] ?? '')) ?>"
                                >
                                    Gửi minh chứng chuyển khoản
                                </a>
                            <?php endif; ?>

                            <a
                                class="btn-link no-print"
                                href="<?= BASE_URL ?>/index.php?route=student/invoice-print&invoice_id=<?= htmlspecialchars((string) ($invoice['id'] ?? '')) ?>"
                                data-i18n="print_invoice"
                            >
                                In hóa đơn
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php endif; ?>
