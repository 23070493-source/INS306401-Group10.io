<h1>Hợp đồng</h1>
<p>Quản lý và theo dõi danh sách hợp đồng KTX. Manager có thể checkout/kết thúc hợp đồng đang active.</p>

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
            <th>Checkout</th>
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
                    <?= htmlspecialchars($contract['semester_name'] ?? '-') ?>
                    <br>
                    <small><?= htmlspecialchars($contract['academic_year'] ?? '-') ?></small>
                </td>

                <td>
                    <?= htmlspecialchars($contract['start_date']) ?>
                    <br>
                    <small>to <?= htmlspecialchars($contract['end_date']) ?></small>
                </td>

                <td><?= number_format((float)$contract['monthly_price']) ?> VND</td>

                <td><?= number_format((float)$contract['deposit_amount']) ?> VND</td>

                <td>
                    <span class="badge <?= htmlspecialchars($contract['status']) ?>">
                        <?= htmlspecialchars($contract['status']) ?>
                    </span>

                    <?php if ($contract['status'] === 'terminated'): ?>
                        <br>
                        <small>
                            Ended:
                            <?= htmlspecialchars($contract['ended_at'] ?? '-') ?>
                        </small>

                        <?php if (!empty($contract['ended_by_username'])): ?>
                            <br>
                            <small>
                                By:
                                <?= htmlspecialchars($contract['ended_by_username']) ?>
                            </small>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($contract['created_by_username'] ?? '-') ?></td>

                <td><?= htmlspecialchars($contract['created_at']) ?></td>

                <td>
                    <?php if ($contract['status'] === 'active'): ?>
                        <form 
                            method="POST" 
                            action="<?= BASE_URL ?>/index.php?route=manager/contract-end"
                            onsubmit="return confirm('Bạn có chắc muốn checkout/kết thúc hợp đồng này không?');"
                        >
                            <input 
                                type="hidden" 
                                name="contract_id" 
                                value="<?= htmlspecialchars($contract['id']) ?>"
                            >

                            <input 
                                type="text" 
                                name="checkout_note" 
                                class="table-input"
                                placeholder="Ghi chú checkout"
                            >

                            <button type="submit" class="btn-reject-small">
                                End Contract
                            </button>
                        </form>
                    <?php elseif ($contract['status'] === 'terminated'): ?>
                        <small>
                            <?= htmlspecialchars($contract['checkout_note'] ?? 'No checkout note') ?>
                        </small>
                    <?php else: ?>
                        <small>-</small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
