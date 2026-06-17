<?php
$totalInvoices = count($invoices ?? []);

$totalAmount = 0;
$totalPaid = 0;
$totalPending = 0;
$totalRemaining = 0;

$paidCount = 0;
$unpaidCount = 0;
$pendingCount = 0;

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

<h1>My Invoices</h1>

<?php if (!$student): ?>
    <div class="alert error">
        Student profile not found.
    </div>

<?php elseif (empty($invoices)): ?>
    <section class="student-invoice-empty">
        <div class="empty-state">
            No invoices found.
        </div>
    </section>

<?php else: ?>

    <section class="student-invoice-hero">
        <div>
            <span class="student-page-label">Billing Overview</span>
            <h2><?= htmlspecialchars($student['full_name'] ?? '-') ?></h2>
        </div>

        <div class="student-invoice-student-info">
            <div>
                <span>Student Code</span>
                <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
            </div>

            <div>
                <span>Faculty</span>
                <strong><?= htmlspecialchars($student['faculty'] ?? '-') ?></strong>
            </div>
        </div>
    </section>

    <section class="student-invoice-summary-grid">
        <div class="student-invoice-summary-card">
            <span>Total Invoices</span>
            <strong><?= htmlspecialchars((string) $totalInvoices) ?></strong>
        </div>

        <div class="student-invoice-summary-card">
            <span>Total Amount</span>
            <strong><?= number_format($totalAmount) ?> VND</strong>
        </div>

        <div class="student-invoice-summary-card">
            <span>Paid Amount</span>
            <strong><?= number_format($totalPaid) ?> VND</strong>
        </div>

        <div class="student-invoice-summary-card">
            <span>Remaining Due</span>
            <strong><?= number_format($totalRemaining) ?> VND</strong>
        </div>
    </section>

    <section class="student-invoice-status-grid">
        <div class="student-invoice-status-card">
            <span>Paid</span>
            <strong><?= htmlspecialchars((string) $paidCount) ?></strong>
        </div>

        <div class="student-invoice-status-card">
            <span>Waiting Confirmation</span>
            <strong><?= htmlspecialchars((string) $pendingCount) ?></strong>
        </div>

        <div class="student-invoice-status-card">
            <span>Unpaid</span>
            <strong><?= htmlspecialchars((string) $unpaidCount) ?></strong>
        </div>

        <div class="student-invoice-status-card">
            <span>Pending Amount</span>
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
                        <span>Invoice</span>
                        <h2><?= htmlspecialchars($invoice['invoice_code'] ?? '-') ?></h2>
                    </div>

                    <span class="badge <?= htmlspecialchars($invoiceStatus) ?>">
                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $invoiceStatus))) ?>
                    </span>
                </div>

                <div class="student-invoice-meta-grid">
                    <div>
                        <span>Contract</span>
                        <strong><?= htmlspecialchars($invoice['contract_code'] ?? '-') ?></strong>
                    </div>

                    <div>
                        <span>Room</span>
                        <strong>
                            <?= htmlspecialchars($invoice['building_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($invoice['room_number'] ?? '-') ?>
                        </strong>
                    </div>

                    <div>
                        <span>Month</span>
                        <strong><?= htmlspecialchars($month) ?></strong>
                    </div>

                    <div>
                        <span>Due Date</span>
                        <strong><?= htmlspecialchars($invoice['due_date'] ?? '-') ?></strong>
                    </div>
                </div>

                <div class="student-invoice-money-grid">
                    <div>
                        <span>Total</span>
                        <strong><?= number_format($total) ?> VND</strong>
                    </div>

                    <div>
                        <span>Paid</span>
                        <strong><?= number_format($paid) ?> VND</strong>
                    </div>

                    <div>
                        <span>Pending</span>
                        <strong><?= number_format($pending) ?> VND</strong>
                    </div>

                    <div>
                        <span>Remaining</span>
                        <strong><?= number_format($remaining) ?> VND</strong>
                    </div>
                </div>

                <div class="student-invoice-action">
                    <?php if ($invoiceStatus === 'paid' || $remaining <= 0): ?>
                        <span class="badge paid">Paid</span>

                    <?php elseif ($pending > 0): ?>
                        <span class="badge pending">Waiting Confirmation</span>

                    <?php else: ?>
                        <a 
                            class="student-primary-link"
                            href="<?= BASE_URL ?>/index.php?route=student/payment-submit&invoice_id=<?= htmlspecialchars((string) ($invoice['id'] ?? '')) ?>"
                        >
                            Submit Bank Transfer
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Invoice Table</h2>
            </div>
        </div>

        <div class="student-table-scroll">
            <table>
                <thead>
                <tr>
                    <th>Invoice Code</th>
                    <th>Contract</th>
                    <th>Room</th>
                    <th>Month</th>
                    <th>Due Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Pending</th>
                    <th>Remaining</th>
                    <th>Status</th>
                    <th>Action</th>
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
                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $invoiceStatus))) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($invoiceStatus === 'paid' || $remaining <= 0): ?>
                                <span class="badge paid">Paid</span>

                            <?php elseif ($pending > 0): ?>
                                <span class="badge pending">Waiting Confirmation</span>

                            <?php else: ?>
                                <a 
                                    class="btn-link"
                                    href="<?= BASE_URL ?>/index.php?route=student/payment-submit&invoice_id=<?= htmlspecialchars((string) ($invoice['id'] ?? '')) ?>"
                                >
                                    Submit Bank Transfer
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php endif; ?>