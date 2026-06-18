<h1>Thanh toán</h1>
<p>Manager xác nhận hoặc từ chối các khoản chuyển khoản sinh viên gửi lên.</p>

<div class="cards">
    <div class="card">
        <h3>Total Payments</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Pending</h3>
        <strong><?= htmlspecialchars($summary['pending']) ?></strong>
    </div>

    <div class="card">
        <h3>Success</h3>
        <strong><?= htmlspecialchars($summary['success']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Rejected</h3>
        <strong><?= htmlspecialchars($summary['rejected']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments">
        All
    </a>

    <a class="filter-link <?= $currentStatus === 'pending' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments&status=pending">
        Pending
    </a>

    <a class="filter-link <?= $currentStatus === 'success' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments&status=success">
        Success
    </a>

    <a class="filter-link <?= $currentStatus === 'rejected' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/payments&status=rejected">
        Rejected
    </a>
</div>

<?php if (empty($payments)): ?>
    <div class="alert error">Không có payment nào phù hợp.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Payment</th>
            <th>Student</th>
            <th>Invoice</th>
            <th>Bank Proof</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Verified By</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= htmlspecialchars($payment['id']) ?></td>

                <td>
                    <strong><?= htmlspecialchars($payment['payment_code']) ?></strong>
                    <br>
                    <small>Method: <?= htmlspecialchars($payment['payment_method'] ?? '-') ?></small>
                    <br>
                    <small>Submitted: <?= htmlspecialchars($payment['payment_date'] ?? '-') ?></small>
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
                        Invoice status:
                        <?= htmlspecialchars($payment['invoice_status']) ?>
                    </small>
                </td>

                <td>
                    <strong>Bank:</strong> <?= htmlspecialchars($payment['sender_bank'] ?? '-') ?>
                    <br>
                    <strong>Account:</strong> <?= htmlspecialchars($payment['sender_account_name'] ?? '-') ?>
                    <br>
                    <strong>Ref:</strong> <?= htmlspecialchars($payment['transaction_reference'] ?? '-') ?>
                    <br>
                    <strong>Note:</strong> <?= htmlspecialchars($payment['note'] ?? '-') ?>
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
                                Approve
                            </button>
                        </form>

                        <form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/payment-reject" class="inline-form reject-inline">
                            <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['id']) ?>">
                            <input
                                type="text"
                                name="rejection_reason"
                                placeholder="Reject reason"
                                class="small-input"
                            >
                            <button type="submit" class="btn-reject-small">
                                Reject
                            </button>
                        </form>

                    <?php elseif ($payment['status'] === 'success'): ?>
                        <span class="badge success">Approved</span>

                    <?php else: ?>
                        <span class="badge rejected">Rejected</span>
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
