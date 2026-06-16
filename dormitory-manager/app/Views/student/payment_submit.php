<h1>Submit Bank Transfer</h1>
<p>Gửi thông tin chuyển khoản để Manager kiểm tra và xác nhận thanh toán.</p>

<?php
$total = (float) $invoice['total_amount'];
$paid = (float) $invoice['paid_amount'];
$remaining = $total - $paid;
?>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="profile-box">
    <h2>Invoice <?= htmlspecialchars($invoice['invoice_code']) ?></h2>
    <p>
        <strong>Student:</strong>
        <?= htmlspecialchars($student['student_code']) ?>
        -
        <?= htmlspecialchars($student['full_name']) ?>
    </p>
    <p><strong>Contract:</strong> <?= htmlspecialchars($invoice['contract_code']) ?></p>
    <p><strong>Room:</strong> <?= htmlspecialchars($invoice['building_name']) ?> - <?= htmlspecialchars($invoice['room_number']) ?></p>
    <p><strong>Month:</strong> <?= htmlspecialchars($invoice['month_year']) ?></p>
    <p><strong>Total:</strong> <?= number_format($total) ?> VND</p>
    <p><strong>Paid:</strong> <?= number_format($paid) ?> VND</p>
    <p><strong>Amount to transfer:</strong> <strong><?= number_format($remaining) ?> VND</strong></p>
</div>

<div class="bank-box">
    <h2>KTX Bank Account</h2>
    <p><strong>Bank:</strong> Vietcombank</p>
    <p><strong>Account Name:</strong> DORMITORY MANAGEMENT CENTER</p>
    <p><strong>Account Number:</strong> 0123456789</p>
    <p>
        <strong>Transfer Content:</strong>
        <?= htmlspecialchars($student['student_code']) ?>
        <?= htmlspecialchars($invoice['invoice_code']) ?>
    </p>
</div>

<form method="POST" action="<?= BASE_URL ?>/index.php?route=student/payment-store" class="form-card wide-form">
    <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($invoice['id']) ?>">

    <h2>Your Bank Transfer Information</h2>

    <label>Sender Bank</label>
    <input
        type="text"
        name="sender_bank"
        required
        placeholder="Ví dụ: Vietcombank, BIDV, Techcombank..."
        value="<?= htmlspecialchars($old['sender_bank'] ?? '') ?>"
    >

    <label>Sender Account Name</label>
    <input
        type="text"
        name="sender_account_name"
        required
        placeholder="Tên chủ tài khoản đã chuyển khoản"
        value="<?= htmlspecialchars($old['sender_account_name'] ?? '') ?>"
    >

    <label>Transaction Reference</label>
    <input
        type="text"
        name="transaction_reference"
        required
        placeholder="Mã giao dịch trên app ngân hàng"
        value="<?= htmlspecialchars($old['transaction_reference'] ?? '') ?>"
    >

    <label>Transfer Time</label>
    <input
        type="datetime-local"
        name="payment_date"
        required
        value="<?= htmlspecialchars($old['payment_date'] ?? date('Y-m-d\TH:i')) ?>"
    >

    <label>Transfer Note</label>
    <textarea
        name="note"
        rows="4"
        placeholder="Nội dung chuyển khoản hoặc ghi chú thêm"
    ><?= htmlspecialchars($old['note'] ?? ($student['student_code'] . ' ' . $invoice['invoice_code'])) ?></textarea>

    <button type="submit">
        Submit Payment Proof
    </button>
</form>

<div class="page-actions">
    <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/my-invoices">
        Back to My Invoices
    </a>
</div>