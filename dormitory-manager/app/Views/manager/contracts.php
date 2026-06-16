<h1>Contracts</h1>
<p>Quản lý và theo dõi danh sách hợp đồng KTX.</p>

<div class="cards">
    <div class="card">
        <h3>Total Contracts</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Active</h3>
        <strong><?= htmlspecialchars($summary['active']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Expired</h3>
        <strong><?= htmlspecialchars($summary['expired']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Terminated</h3>
        <strong><?= htmlspecialchars($summary['terminated']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts">
        All
    </a>

    <a class="filter-link <?= $currentStatus === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=active">
        Active
    </a>

    <a class="filter-link <?= $currentStatus === 'expired' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=expired">
        Expired
    </a>

    <a class="filter-link <?= $currentStatus === 'terminated' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=terminated">
        Terminated
    </a>

    <a class="filter-link <?= $currentStatus === 'cancelled' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=cancelled">
        Cancelled
    </a>
</div>

<?php if (empty($contracts)): ?>
    <div class="alert error">Không có hợp đồng nào phù hợp.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Contract Code</th>
            <th>Student</th>
            <th>Room</th>
            <th>Semester</th>
            <th>Duration</th>
            <th>Monthly Price</th>
            <th>Deposit</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Created At</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($contracts as $contract): ?>
            <tr>
                <td><?= htmlspecialchars($contract['id']) ?></td>
                <td>
                    <strong><?= htmlspecialchars($contract['contract_code']) ?></strong>
                </td>
                <td>
                    <?= htmlspecialchars($contract['student_code']) ?>
                    <br>
                    <small><?= htmlspecialchars($contract['full_name']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($contract['building_name']) ?>
                    -
                    <?= htmlspecialchars($contract['room_number']) ?>
                    <br>
                    <small>
                        <?= htmlspecialchars($contract['room_type']) ?>
                        /
                        <?= htmlspecialchars($contract['gender']) ?>
                    </small>
                </td>
                <td>
                    <?= htmlspecialchars($contract['semester_name']) ?>
                    <br>
                    <small><?= htmlspecialchars($contract['academic_year']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($contract['start_date']) ?>
                    <br>
                    <small>to <?= htmlspecialchars($contract['end_date']) ?></small>
                </td>
                <td><?= number_format($contract['monthly_price']) ?> VND</td>
                <td><?= number_format($contract['deposit_amount']) ?> VND</td>
                <td>
                    <span class="badge <?= htmlspecialchars($contract['status']) ?>">
                        <?= htmlspecialchars($contract['status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($contract['created_by_username'] ?? '-') ?></td>
                <td><?= htmlspecialchars($contract['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>