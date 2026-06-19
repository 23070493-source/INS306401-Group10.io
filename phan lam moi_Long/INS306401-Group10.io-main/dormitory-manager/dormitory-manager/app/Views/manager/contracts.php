<h1>Hợp đồng</h1>
<p>Quản lý và theo dõi danh sách hợp đồng KTX. Quản lý có thể kết thúc hợp đồng đang hiệu lực.</p>

<div class="cards">
    <div class="card">
        <h3>Tổng hợp đồng</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Đang hiệu lực</h3>
        <strong><?= htmlspecialchars($summary['active']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Hết hạn</h3>
        <strong><?= htmlspecialchars($summary['expired']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Đã chấm dứt</h3>
        <strong><?= htmlspecialchars($summary['terminated']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts">
        Tất cả
    </a>

    <a class="filter-link <?= $currentStatus === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=active">
        Đang hiệu lực
    </a>

    <a class="filter-link <?= $currentStatus === 'expired' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=expired">
        Hết hạn
    </a>

    <a class="filter-link <?= $currentStatus === 'terminated' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=terminated">
        Đã chấm dứt
    </a>

    <a class="filter-link <?= $currentStatus === 'cancelled' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/contracts&status=cancelled">
        Đã hủy
    </a>
</div>

<?php if (empty($contracts)): ?>
    <div class="alert error">Không có hợp đồng nào phù hợp.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Mã hợp đồng</th>
            <th>Sinh viên</th>
            <th>Phòng</th>
            <th>Học kỳ</th>
            <th>Thời hạn</th>
            <th>Giá hàng tháng</th>
            <th>Tiền cọc</th>
            <th>Trạng thái</th>
            <th>Người tạo</th>
            <th>Ngày tạo</th>
            <th>Kết thúc</th>
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
                    <small>đến <?= htmlspecialchars($contract['end_date']) ?></small>
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
                            Kết thúc:
                            <?= htmlspecialchars($contract['ended_at'] ?? '-') ?>
                        </small>

                        <?php if (!empty($contract['ended_by_username'])): ?>
                            <br>
                            <small>
                                Bởi:
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
                            onsubmit="return confirm('Bạn có chắc muốn kết thúc hợp đồng này không?');"
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
                                placeholder="Ghi chú kết thúc"
                            >

                            <button type="submit" class="btn-reject-small">
                                Kết thúc hợp đồng
                            </button>
                        </form>
                    <?php elseif ($contract['status'] === 'terminated'): ?>
                        <small>
                            <?= htmlspecialchars($contract['checkout_note'] ?? 'Không có ghi chú kết thúc') ?>
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
