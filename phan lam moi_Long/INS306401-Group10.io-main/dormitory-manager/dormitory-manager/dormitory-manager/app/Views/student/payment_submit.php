<?php
$total = (float) $invoice['total_amount'];
$paid = (float) $invoice['paid_amount'];
$remaining = max(0, $total - $paid);
$transferContent = trim(($student['student_code'] ?? '') . ' ' . ($invoice['invoice_code'] ?? ''));
$oldMethod = $old['payment_method'] ?? 'bank_transfer';

// Demo QR settings. Change these values to the dormitory bank account if needed.
$bankBin = '970436'; // Vietcombank BIN for demo QR
$receiverAccount = '0123456789';
$receiverName = 'DORMITORY MANAGEMENT CENTER';
$qrUrl = 'https://img.vietqr.io/image/'
    . rawurlencode($bankBin)
    . '-'
    . rawurlencode($receiverAccount)
    . '-compact2.png?amount='
    . rawurlencode((string) (int) round($remaining))
    . '&addInfo='
    . rawurlencode($transferContent)
    . '&accountName='
    . rawurlencode($receiverName);
?>

<section class="manager-dashboard role-dashboard payment-submit-page">
    <div class="dashboard-hero">
        <div>
            <span class="dashboard-eyebrow" data-i18n="my_invoices">Hóa đơn của tôi</span>
            <h1 data-i18n="student_payment_submit">Gửi thông tin thanh toán</h1>
            <p data-i18n="student_payment_submit_intro">
                Chọn phương thức thanh toán. Nếu chuyển khoản, vui lòng quét QR và upload ảnh bill để Manager xác nhận.
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
                    <span data-i18n="amount_to_transfer">Số tiền cần thanh toán</span>
                    <strong><?= number_format($remaining) ?> VND</strong>
                </div>
            </div>
        </section>

        <section class="dashboard-panel bank-instruction-panel">
            <div class="panel-heading">
                <span>Hướng dẫn</span>
                <strong>Manager sẽ xác nhận sau khi kiểm tra</strong>
            </div>

            <p>
                Với chuyển khoản ngân hàng, bạn có thể quét QR và upload ảnh bill chuyển khoản. Với tiền mặt, bạn gửi yêu cầu trước rồi đến văn phòng KTX để thanh toán trực tiếp.
            </p>
        </section>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=student/payment-store" class="dashboard-panel payment-form-panel" enctype="multipart/form-data" data-payment-form>
        <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($invoice['id']) ?>">
        <input type="hidden" name="transfer_content" value="<?= htmlspecialchars($transferContent) ?>">

        <div class="panel-heading">
            <span>Phương thức thanh toán</span>
            <strong>Chọn một phương thức</strong>
        </div>

        <div class="payment-method-box" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin:16px 0 22px;">
            <label class="payment-method-option" style="border:1px solid #d6dce8;border-radius:14px;padding:18px;background:#fff;cursor:pointer;">
                <input type="radio" name="payment_method" value="bank_transfer" <?= $oldMethod !== 'cash' ? 'checked' : '' ?>>
                <strong>Chuyển khoản ngân hàng</strong>
                <p style="margin:8px 0 0;color:#64748b;">Quét QR, chuyển khoản và upload ảnh bill.</p>
            </label>

            <label class="payment-method-option" style="border:1px solid #d6dce8;border-radius:14px;padding:18px;background:#fff;cursor:pointer;">
                <input type="radio" name="payment_method" value="cash" <?= $oldMethod === 'cash' ? 'checked' : '' ?>>
                <strong>Tiền mặt tại trường</strong>
                <p style="margin:8px 0 0;color:#64748b;">Đến văn phòng KTX để nộp tiền và chờ Manager xác nhận.</p>
            </label>
        </div>

        <section data-payment-panel="bank_transfer">
            <div class="panel-heading">
                <span>QR chuyển khoản</span>
                <strong>Thông tin chuyển khoản ngân hàng</strong>
            </div>

            <div class="payment-qr-panel" style="display:grid;grid-template-columns:240px minmax(0,1fr);gap:24px;align-items:center;margin:18px 0 24px;padding:20px;border:1px solid #d6dce8;border-radius:16px;background:#fff;">
                <div style="padding:12px;border:1px solid #e2e8f0;border-radius:14px;background:#f8fafc;text-align:center;">
                    <img src="<?= htmlspecialchars($qrUrl) ?>" alt="QR chuyển khoản" style="max-width:210px;width:100%;height:auto;border-radius:10px;">
                </div>

                <div>
                    <div style="display:grid;grid-template-columns:170px minmax(0,1fr);gap:10px;padding:8px 0;border-bottom:1px dashed #d6dce8;">
                        <strong>Ngân hàng nhận</strong>
                        <span>Vietcombank</span>
                    </div>
                    <div style="display:grid;grid-template-columns:170px minmax(0,1fr);gap:10px;padding:8px 0;border-bottom:1px dashed #d6dce8;">
                        <strong>Tên tài khoản</strong>
                        <span><?= htmlspecialchars($receiverName) ?></span>
                    </div>
                    <div style="display:grid;grid-template-columns:170px minmax(0,1fr);gap:10px;padding:8px 0;border-bottom:1px dashed #d6dce8;">
                        <strong>Số tài khoản</strong>
                        <span><?= htmlspecialchars($receiverAccount) ?></span>
                    </div>
                    <div style="display:grid;grid-template-columns:170px minmax(0,1fr);gap:10px;padding:8px 0;border-bottom:1px dashed #d6dce8;">
                        <strong>Số tiền</strong>
                        <span><?= number_format($remaining) ?> VND</span>
                    </div>
                    <div style="display:grid;grid-template-columns:170px minmax(0,1fr);gap:10px;padding:8px 0;">
                        <strong>Nội dung CK</strong>
                        <span><?= htmlspecialchars($transferContent) ?></span>
                    </div>
                </div>
            </div>

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
                        data-bank-required="1"
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
                        data-bank-required="1"
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
                        data-bank-required="1"
                        data-i18n-placeholder="transaction_code_placeholder"
                        placeholder="Mã giao dịch trên app ngân hàng"
                        value="<?= htmlspecialchars($old['transaction_reference'] ?? '') ?>"
                    >
                </div>

                <div class="field-group">
                    <label>Ảnh bill chuyển khoản</label>
                    <input
                        type="file"
                        name="payment_proof_image"
                        accept="image/jpeg,image/png,image/webp"
                        data-bank-required="1"
                    >
                    <small>Chấp nhận JPG, PNG hoặc WEBP. Tối đa 5MB.</small>
                </div>
            </div>
        </section>

        <section data-payment-panel="cash" style="display:none;">
            <div class="readonly-note" style="margin:18px 0 24px;padding:18px 20px;border:1px solid #f2cf68;border-radius:14px;background:#fff8df;color:#7a5200;font-weight:700;">
                Bạn đã chọn thanh toán tiền mặt tại trường. Sau khi gửi yêu cầu, vui lòng đến văn phòng KTX để nộp tiền. Manager sẽ xác nhận thanh toán sau khi nhận tiền.
            </div>
        </section>

        <div class="field-group">
            <label data-i18n="transfer_note">Ghi chú</label>
            <textarea
                name="note"
                rows="4"
                data-i18n-placeholder="transfer_note_placeholder"
                placeholder="Nội dung chuyển khoản hoặc ghi chú thêm"
            ><?= htmlspecialchars($old['note'] ?? $transferContent) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" data-i18n="submit_payment_proof">
                Gửi thông tin thanh toán
            </button>

            <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/my-invoices" data-i18n="back_to_my_invoices">
                Quay lại hóa đơn của tôi
            </a>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-payment-form]');

    if (!form) {
        return;
    }

    const methodInputs = form.querySelectorAll('input[name="payment_method"]');
    const bankPanel = form.querySelector('[data-payment-panel="bank_transfer"]');
    const cashPanel = form.querySelector('[data-payment-panel="cash"]');
    const bankFields = form.querySelectorAll('[data-bank-required="1"]');

    function currentMethod() {
        const checked = form.querySelector('input[name="payment_method"]:checked');
        return checked ? checked.value : 'bank_transfer';
    }

    function syncPaymentMethod() {
        const method = currentMethod();
        const isBank = method === 'bank_transfer';

        if (bankPanel) {
            bankPanel.style.display = isBank ? '' : 'none';
        }

        if (cashPanel) {
            cashPanel.style.display = isBank ? 'none' : '';
        }

        bankFields.forEach(function (field) {
            field.required = isBank;
            field.disabled = !isBank;
        });
    }

    methodInputs.forEach(function (input) {
        input.addEventListener('change', syncPaymentMethod);
    });

    syncPaymentMethod();
});
</script>
