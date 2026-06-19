<?php
$total = (float) $invoice['total_amount'];
$paid = (float) $invoice['paid_amount'];
$remaining = $total - $paid;
$transferContent = trim(($student['student_code'] ?? '') . ' ' . ($invoice['invoice_code'] ?? ''));
?>

<section class="manager-dashboard role-dashboard payment-submit-page">
    <div class="dashboard-hero">
        <div>
            <span class="dashboard-eyebrow" data-i18n="my_invoices">Hóa đơn của tôi</span>
            <h1 data-i18n="student_payment_submit">Gửi thông tin chuyển khoản</h1>
            <p data-i18n="student_payment_submit_intro">
                Gửi thông tin chuyển khoản để quản lý kiểm tra và xác nhận thanh toán.
            </p>
        </div>

        <div class="dashboard-status-card">
            <span data-i18n="invoice">Hóa đơn</span>
            <strong><?= htmlspecialchars($invoice['invoice_code']) ?></strong>
            <small data-i18n="payment_time_auto_note">Thời gian gửi sẽ được hệ thống ghi nhận tự động khi bạn bấm gửi.</small>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="payment-summary-grid">
        <section class="dashboard-panel payment-summary-panel">
            <div class="panel-heading">
                <span data-i18n="invoice_information">Thông tin hóa đơn</span>
                <strong><?= htmlspecialchars($invoice['invoice_code']) ?></strong>
            </div>

            <div class="student-room-summary">
                <div>
                    <span data-i18n="student">Sinh viên</span>
                    <strong><?= htmlspecialchars($student['student_code']) ?> - <?= htmlspecialchars($student['full_name']) ?></strong>
                </div>

                <div>
                    <span data-i18n="contract">Hợp đồng</span>
                    <strong><?= htmlspecialchars($invoice['contract_code']) ?></strong>
                </div>

                <div>
                    <span data-i18n="room">Phòng</span>
                    <strong><?= htmlspecialchars($invoice['building_name']) ?> - <?= htmlspecialchars($invoice['room_number']) ?></strong>
                </div>

                <div>
                    <span data-i18n="month">Tháng</span>
                    <strong><?= htmlspecialchars($invoice['month_year']) ?></strong>
                </div>

                <div>
                    <span data-i18n="total_amount">Tổng tiền</span>
                    <strong><?= number_format($total) ?> VND</strong>
                </div>

                <div>
                    <span data-i18n="paid_amount">Đã thanh toán</span>
                    <strong><?= number_format($paid) ?> VND</strong>
                </div>

                <div>
                    <span data-i18n="amount_to_transfer">Số tiền cần chuyển</span>
                    <strong><?= number_format($remaining) ?> VND</strong>
                </div>
            </div>
        </section>

        <section class="dashboard-panel bank-instruction-panel">
            <div class="panel-heading">
                <span data-i18n="bank_transfer">Chuyển khoản ngân hàng</span>
                <strong data-i18n="dormitory_bank_account">Tài khoản KTX</strong>
            </div>

            <div class="bank-detail-list">
                <div>
                    <span data-i18n="bank">Ngân hàng</span>
                    <strong>Vietcombank</strong>
                </div>

                <div>
                    <span data-i18n="account_name">Tên tài khoản</span>
                    <strong>DORMITORY MANAGEMENT CENTER</strong>
                </div>

                <div>
                    <span data-i18n="account_number">Số tài khoản</span>
                    <strong>0123456789</strong>
                </div>

                <div>
                    <span data-i18n="transfer_content">Nội dung chuyển khoản</span>
                    <strong><?= htmlspecialchars($transferContent) ?></strong>
                </div>
            </div>
        </section>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=student/payment-store" class="dashboard-panel payment-form-panel">
        <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($invoice['id']) ?>">

        <div class="panel-heading">
            <span data-i18n="payment_proof">Minh chứng thanh toán</span>
            <strong data-i18n="your_bank_transfer_information">Thông tin chuyển khoản của bạn</strong>
        </div>

        <div class="form-grid">
            <div class="field-group">
                <label data-i18n="sender_bank">Ngân hàng chuyển</label>
                <input
                    type="text"
                    name="sender_bank"
                    required
                    data-i18n-placeholder="bank_example_placeholder"
                    placeholder="Ví dụ: Vietcombank, BIDV, Techcombank..."
                    value="<?= htmlspecialchars($old['sender_bank'] ?? '') ?>"
                >
            </div>

            <div class="field-group">
                <label data-i18n="sender_account_name">Tên chủ tài khoản</label>
                <input
                    type="text"
                    name="sender_account_name"
                    required
                    data-i18n-placeholder="account_owner_placeholder"
                    placeholder="Tên chủ tài khoản đã chuyển khoản"
                    value="<?= htmlspecialchars($old['sender_account_name'] ?? '') ?>"
                >
            </div>

            <div class="field-group">
                <label data-i18n="transaction_reference">Mã giao dịch</label>
                <input
                    type="text"
                    name="transaction_reference"
                    required
                    data-i18n-placeholder="transaction_code_placeholder"
                    placeholder="Mã giao dịch trên app ngân hàng"
                    value="<?= htmlspecialchars($old['transaction_reference'] ?? '') ?>"
                >
            </div>

            <div class="field-group">
                <label data-i18n="auto_submit_time">Thời gian gửi</label>
                <div class="readonly-note" data-i18n="payment_time_auto_note">
                    Thời gian gửi sẽ được hệ thống ghi nhận tự động khi bạn bấm gửi.
                </div>
            </div>
        </div>

        <div class="field-group">
            <label data-i18n="transfer_note">Ghi chú chuyển khoản</label>
            <textarea
                name="note"
                rows="4"
                data-i18n-placeholder="transfer_note_placeholder"
                placeholder="Nội dung chuyển khoản hoặc ghi chú thêm"
            ><?= htmlspecialchars($old['note'] ?? $transferContent) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" data-i18n="submit_payment_proof">
                Gửi minh chứng thanh toán
            </button>

            <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/my-invoices" data-i18n="back_to_my_invoices">
                Quay lại hóa đơn của tôi
            </a>
        </div>
    </form>
</section>
