<h1>My Invoices</h1>
<p>Xem hóa đơn và gửi thông tin chuyển khoản để Manager xác nhận.</p>

<?php if (!$student): ?>
    <div class="alert error">Không tìm thấy hồ sơ sinh viên.</div>

<?php elseif (empty($invoices)): ?>
    <div class="alert error">
        Bạn chưa có hóa đơn nào.
    </div>

<?php else: ?>

    <div class="profile-box">
        <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($student['student_code']) ?></p>
        <p><strong>Khoa:</strong> <?= htmlspecialchars($student['faculty'] ?? '-') ?></p>
    </div>

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
            $total = (float) $invoice['total_amount'];
            $paid = (float) $invoice['paid_amount'];
            $pending = (float) ($invoice['pending_amount'] ?? 0);
            $remaining = $total - $paid;
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($invoice['invoice_code']) ?></strong>
                </td>

                <td><?= htmlspecialchars($invoice['contract_code']) ?></td>

                <td>
                    <?= htmlspecialchars($invoice['building_name']) ?>
                    -
                    <?= htmlspecialchars($invoice['room_number']) ?>
                </td>

                <td><?= htmlspecialchars($invoice['month_year']) ?></td>

                <td><?= htmlspecialchars($invoice['due_date']) ?></td>

                <td><?= number_format($total) ?> VND</td>

                <td><?= number_format($paid) ?> VND</td>

                <td><?= number_format($pending) ?> VND</td>

                <td><?= number_format($remaining) ?> VND</td>

                <td>
                    <span class="badge <?= htmlspecialchars($invoice['status']) ?>">
                        <?= htmlspecialchars($invoice['status']) ?>
                    </span>
                </td>

                <td>
                    <?php if ($invoice['status'] === 'paid'): ?>
                        <span class="badge paid">Paid</span>

                    <?php elseif ($pending > 0): ?>
                        <span class="badge pending">Waiting confirmation</span>

                    <?php elseif ($remaining > 0): ?>
                        <a 
                            class="btn-link"
                            href="<?= BASE_URL ?>/index.php?route=student/payment-submit&invoice_id=<?= htmlspecialchars($invoice['id']) ?>"
                        >
                            Submit Bank Transfer
                        </a>

                    <?php else: ?>
                        <span class="badge paid">Paid</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>