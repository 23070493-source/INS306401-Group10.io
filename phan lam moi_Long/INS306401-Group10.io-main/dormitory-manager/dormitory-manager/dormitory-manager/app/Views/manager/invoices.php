<h1>Hóa đơn</h1>
<p>Quản lý hóa đơn KTX của sinh viên.</p>

<div class="page-actions">
    <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=manager/invoice-create">
        Tạo hóa đơn
    </a>
</div>

<div class="cards">
    <div class="card">
        <h3>Tổng hóa đơn</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Chưa thanh toán</h3>
        <strong><?= htmlspecialchars($summary['unpaid']) ?></strong>
    </div>

    <div class="card">
        <h3>Đã thanh toán</h3>
        <strong><?= htmlspecialchars($summary['paid']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Quá hạn</h3>
        <strong><?= htmlspecialchars($summary['overdue']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Thanh toán một phần</h3>
        <strong><?= htmlspecialchars($summary['partially_paid']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices">
        Tất cả
    </a>

    <a class="filter-link <?= $currentStatus === 'unpaid' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=unpaid">
        Chưa thanh toán
    </a>

    <a class="filter-link <?= $currentStatus === 'paid' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=paid">
        Đã thanh toán
    </a>

    <a class="filter-link <?= $currentStatus === 'partially_paid' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=partially_paid">
        Thanh toán một phần
    </a>

    <a class="filter-link <?= $currentStatus === 'overdue' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/invoices&status=overdue">
        Quá hạn
    </a>
</div>

<?php if (empty($invoices)): ?>
    <div class="alert error">Không có hóa đơn nào phù hợp.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Mã hóa đơn</th>
            <th>Sinh viên</th>
            <th>Phòng</th>
            <th>Tháng</th>
            <th>Hạn thanh toán</th>
            <th>Tổng tiền</th>
            <th>Đã thanh toán</th>
            <th>Còn lại</th>
            <th>Trạng thái</th>
            <th>Người tạo</th>
            <th>In</th>
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
                <td>
                    <a
                        class="btn-link no-print"
                        href="<?= BASE_URL ?>/index.php?route=manager/invoice-print&invoice_id=<?= htmlspecialchars((string) $invoice['id']) ?>"
                        data-i18n="print_invoice"
                    >
                        In hóa đơn
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
