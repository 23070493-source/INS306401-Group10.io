<?php
$status = $contract['status'] ?? '-';
$monthlyPrice = (float) ($contract['monthly_price'] ?? 0);
$depositAmount = (float) ($contract['deposit_amount'] ?? 0);
?>

<section class="print-document">
    <div class="print-toolbar no-print">
        <button type="button" class="btn-print" onclick="window.print()" data-i18n="print_contract">
            In hợp đồng
        </button>
        <a class="btn-link" href="<?= htmlspecialchars($backUrl ?? (BASE_URL . '/index.php')) ?>" data-i18n="go_back">
            Quay lại
        </a>
    </div>

    <article class="document-paper">
        <header class="document-header">
            <img src="<?= BASE_URL ?>/assets/img/vnu-is-logo.jpg" alt="VNU-IS">
            <div>
                <span>Hệ thống quản lý ký túc xá VNU-IS</span>
                <h1 data-i18n="print_contract_title">Hợp đồng ký túc xá</h1>
                <p data-i18n="print_contract_subtitle">Bản in phục vụ đối chiếu thông tin cư trú sinh viên.</p>
            </div>
        </header>

        <section class="document-highlight">
            <div>
                <span data-i18n="contract_code">Mã hợp đồng</span>
                <strong><?= htmlspecialchars($contract['contract_code'] ?? '-') ?></strong>
            </div>
            <div>
                <span data-i18n="status">Trạng thái</span>
                <strong><?= htmlspecialchars($status) ?></strong>
            </div>
            <div>
                <span data-i18n="created_at">Ngày tạo</span>
                <strong><?= htmlspecialchars($contract['created_at'] ?? '-') ?></strong>
            </div>
        </section>

        <section class="document-section">
            <h2 data-i18n="student_information">Thông tin sinh viên</h2>
            <div class="document-grid">
                <div>
                    <span data-i18n="student_code">Mã sinh viên</span>
                    <strong><?= htmlspecialchars($contract['student_code'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="full_name">Họ và tên</span>
                    <strong><?= htmlspecialchars($contract['full_name'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="gender">Giới tính</span>
                    <strong><?= htmlspecialchars($contract['student_gender'] ?? $contract['gender'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="faculty">Khoa/Viện</span>
                    <strong><?= htmlspecialchars($contract['faculty'] ?? '-') ?></strong>
                </div>
            </div>
        </section>

        <section class="document-section">
            <h2 data-i18n="room_information">Thông tin phòng ở</h2>
            <div class="document-grid">
                <div>
                    <span data-i18n="building">Tòa nhà</span>
                    <strong><?= htmlspecialchars($contract['building_name'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="room">Phòng</span>
                    <strong><?= htmlspecialchars($contract['room_number'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="room_type">Loại phòng</span>
                    <strong><?= htmlspecialchars($contract['room_type'] ?? '-') ?></strong>
                </div>
                <div>
                    <span data-i18n="capacity">Sức chứa</span>
                    <strong><?= htmlspecialchars((string) ($contract['capacity'] ?? '-')) ?></strong>
                </div>
            </div>
        </section>

        <section class="document-section">
            <h2 data-i18n="contract_terms">Điều khoản tài chính và thời hạn</h2>
            <div class="document-grid">
                <div>
                    <span data-i18n="semester">Học kỳ</span>
                    <strong>
                        <?= htmlspecialchars($contract['semester_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($contract['academic_year'] ?? '-') ?>
                    </strong>
                </div>
                <div>
                    <span data-i18n="duration">Thời hạn</span>
                    <strong>
                        <?= htmlspecialchars($contract['start_date'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($contract['end_date'] ?? '-') ?>
                    </strong>
                </div>
                <div>
                    <span data-i18n="monthly_price">Giá hàng tháng</span>
                    <strong><?= number_format($monthlyPrice) ?> VND</strong>
                </div>
                <div>
                    <span data-i18n="deposit">Tiền cọc</span>
                    <strong><?= number_format($depositAmount) ?> VND</strong>
                </div>
            </div>
        </section>

        <section class="signature-grid">
            <div>
                <strong data-i18n="student_signature">Sinh viên</strong>
                <span data-i18n="sign_and_full_name">Ký và ghi rõ họ tên</span>
            </div>
            <div>
                <strong data-i18n="manager_signature">Quản lý KTX</strong>
                <span data-i18n="sign_and_full_name">Ký và ghi rõ họ tên</span>
            </div>
        </section>
    </article>
</section>
