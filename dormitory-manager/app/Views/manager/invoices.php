<h1>Invoices</h1>
<p>Quản lý hóa đơn KTX của sinh viên.</p>

<div class="page-actions">
    <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=manager/invoice-create">
        Create Invoice
    </a>
</div>

<div class="cards">
    <div class="card">
        <h3>Total Invoices</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Unpaid</h3>
        <strong><?= htmlspecialchars($summary['unpaid']) ?></strong>
    </div>

    <div class="card">
        <h3>Paid</h3>
        <strong><?= htmlspecialchars($summary['paid']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Overdue</h3>
        <strong><?= htmlspecialchars($summary['overdue']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Partially Paid</h3>
        <strong><?= htmlspecialchars($summary['partially_paid']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices">
        All
    </a>

    <a class="filter-link <?= $currentStatus === 'unpaid' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=unpaid">
        Unpaid
    </a>

    <a class="filter-link <?= $currentStatus === 'paid' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=paid">
        Paid
    </a>

    <a class="filter-link <?= $currentStatus === 'partially_paid' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=partially_paid">
        Partially Paid
    </a>

    <a class="filter-link <?= $currentStatus === 'overdue' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=overdue">
        Overdue
    </a>
</div>

<?php if (empty($invoices)): ?>
    <div class="alert error">Không có hóa đơn nào phù hợp.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Invoice Code</th>
            <th>Student</th>
            <th>Room</th>
            <th>Month</th>
            <th>Due Date</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Remaining</th>
            <th>Status</th>
            <th>Created By</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($invoices as $invoice): ?>
            <?php
            $remaining = (float) $invoice['total_amount'] - (float) $invoice['paid_amount'];
            ?>
            <tr>
                <td><?= htmlspecialchars($invoice['id']) ?></td>
                <td>
                    <strong><?= htmlspecialchars($invoice['invoice_code']) ?></strong>
                    <br>
                    <small><?= htmlspecialchars($invoice['contract_code']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($invoice['student_code']) ?>
                    <br>
                    <small><?= htmlspecialchars($invoice['full_name']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($invoice['building_name']) ?>
                    -
                    <?= htmlspecialchars($invoice['room_number']) ?>
                </td>
                <td><?= htmlspecialchars($invoice['month_year']) ?></td>
                <td><?= htmlspecialchars($invoice['due_date']) ?></td>
                <td><?= number_format($invoice['total_amount']) ?> VND</td>
                <td><?= number_format($invoice['paid_amount']) ?> VND</td>
                <td><?= number_format($remaining) ?> VND</td>
                <td>
                    <span class="badge <?= htmlspecialchars($invoice['status']) ?>">
                        <?= htmlspecialchars($invoice['status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($invoice['created_by_username'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>