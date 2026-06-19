<h1>Thanh toán</h1>
<p>Quản lý xác nhận hoặc từ chối các khoản chuyển khoản sinh viên gửi lên.</p>

<div class="cards">
    <div class="card">
        <h3>Tổng thanh toán</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Chờ xử lý</h3>
        <strong><?= htmlspecialchars($summary['pending']) ?></strong>
    </div>

    <div class="card">
        <h3>Thành công</h3>
        <strong><?= htmlspecialchars($summary['success']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Từ chối</h3>
        <strong><?= htmlspecialchars($summary['rejected']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments">
        Tất cả
    </a>

    <a class="filter-link <?= $currentStatus === 'pending' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments&status=pending">
        Chờ xử lý
    </a>

    <a class="filter-link <?= $currentStatus === 'success' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments&status=success">
        Thành công
    </a>

    <a class="filter-link <?= $currentStatus === 'rejected' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments&status=rejected">
        Từ chối
    </a>
</div>

<?php if (empty($payments)): ?>
    <div class="alert error">Không có thanh toán nào phù hợp.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Thanh toán</th>
            <th>Sinh viên</th>
            <th>Hóa đơn</th>
            <th>Thông tin chuyển khoản</th>
            <th>Số tiền</th>
            <th>Trạng thái</th>
            <th>Người xác nhận</th>
            <th>Thao tác</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= htmlspecialchars($payment['id']) ?></td>

                <td>
                    <strong><?= htmlspecialchars($payment['payment_code']) ?></strong>
                    <br>
                    <small>Phương thức: <?= htmlspecialchars($payment['payment_method'] ?? '-') ?></small>
                    <br>
                    <small>Ngày gửi: <?= htmlspecialchars($payment['payment_date'] ?? '-') ?></small>
                </td>

                <td>
                    <?= htmlspecialchars($payment['student_code']) ?>
                    <br>
                    <small><?= htmlspecialchars($payment['full_name']) ?></small>
                </td>

                <td>
                    <?= htmlspecialchars($payment['invoice_code']) ?>
                    <br>
                    <small>
                        Trạng thái hóa đơn:
                        <?= htmlspecialchars($payment['invoice_status']) ?>
                    </small>
                </td>

                <td>
                    <strong>Ngân hàng:</strong> <?= htmlspecialchars($payment['sender_bank'] ?? '-') ?>
                    <br>
                    <strong>Tài khoản:</strong> <?= htmlspecialchars($payment['sender_account_name'] ?? '-') ?>
                    <br>
                    <strong>Mã giao dịch:</strong> <?= htmlspecialchars($payment['transaction_reference'] ?? '-') ?>
                    <br>
                    <strong>Ghi chú:</strong> <?= htmlspecialchars($payment['note'] ?? '-') ?>
                </td>

                <td><?= number_format($payment['amount']) ?> VND</td>

                <td>
                    <span class="badge <?= htmlspecialchars($payment['status']) ?>">
                        <?= htmlspecialchars($payment['status']) ?>
                    </span>
                </td>

                <td>
                    <?= htmlspecialchars($payment['verified_by_username'] ?? '-') ?>
                    <?php if (!empty($payment['verified_at'])): ?>
                        <br>
                        <small><?= htmlspecialchars($payment['verified_at']) ?></small>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($payment['status'] === 'pending'): ?>
                        <form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/payment-approve" class="inline-form">
                            <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['id']) ?>">
                            <button type="submit" class="btn-pay">
                                Xác nhận
                            </button>
                        </form>

                        <form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/payment-reject" class="inline-form reject-inline">
                            <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['id']) ?>">
                            <input
                                type="text"
                                name="rejection_reason"
                                placeholder="Lý do từ chối"
                                class="small-input"
                            >
                            <button type="submit" class="btn-reject-small">
                                Từ chối
                            </button>
                        </form>

                    <?php elseif ($payment['status'] === 'success'): ?>
                        <span class="badge success">Đã duyệt</span>

                    <?php else: ?>
                        <span class="badge rejected">Từ chối</span>
                        <?php if (!empty($payment['rejection_reason'])): ?>
                            <br>
                            <small><?= htmlspecialchars($payment['rejection_reason']) ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
