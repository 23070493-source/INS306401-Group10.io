<?php
$statusLabels = [
    'active' => 'Đang hiệu lực',
    'expired' => 'Hết hạn',
    'terminated' => 'Đã chấm dứt',
    'cancelled' => 'Đã hủy',
];

$genderLabels = [
    'male' => 'Nam',
    'female' => 'Nữ',
    'Nam' => 'Nam',
    'Nữ' => 'Nữ',
];

$roomTypeLabels = [
    'standard' => 'Tiêu chuẩn',
    'premium' => 'Cao cấp',
];

$status = (string) ($contract['status'] ?? '-');
$gender = (string) ($contract['student_gender'] ?? $contract['gender'] ?? '-');
$roomGender = (string) ($contract['gender'] ?? '-');
$roomType = (string) ($contract['room_type'] ?? '-');
$monthlyPrice = (float) ($contract['monthly_price'] ?? 0);
$depositAmount = (float) ($contract['deposit_amount'] ?? 0);
$createdAt = (string) ($contract['created_at'] ?? date('Y-m-d'));
?>

<section class="print-document a4-document">
    <div class="print-toolbar no-print">
        <button type="button" class="btn-print" onclick="window.print()">
            In / Lưu PDF hợp đồng
        </button>
        <a class="btn-link" href="<?= htmlspecialchars($backUrl ?? (BASE_URL . '/index.php')) ?>">
            Quay lại
        </a>
    </div>

    <article class="document-paper a4-paper contract-paper compact-contract-paper">
        <header class="contract-print-header">
            <img class="contract-print-logo" src="<?= BASE_URL ?>/assets/img/vnu-is-logo.jpg" alt="VNU-IS">
            <div class="contract-print-title">
                <span>Hệ thống quản lý ký túc xá VNU-IS</span>
                <h1>HỢP ĐỒNG KÝ TÚC XÁ</h1>
                <p>Bản in phục vụ xác nhận thông tin cư trú, tài chính và trách nhiệm của sinh viên.</p>
            </div>
        </header>

        <section class="contract-meta-strip">
            <div>
                <span>Mã hợp đồng</span>
                <strong><?= htmlspecialchars($contract['contract_code'] ?? '-') ?></strong>
            </div>
            <div>
                <span>Trạng thái</span>
                <strong><?= htmlspecialchars($statusLabels[$status] ?? $status) ?></strong>
            </div>
            <div>
                <span>Ngày lập</span>
                <strong><?= htmlspecialchars($createdAt) ?></strong>
            </div>
        </section>

        <section class="contract-section">
            <h2>1. Thông tin sinh viên</h2>
            <table class="contract-info-table">
                <tbody>
                <tr>
                    <th>Mã sinh viên</th>
                    <td><?= htmlspecialchars($contract['student_code'] ?? '-') ?></td>
                    <th>Họ và tên</th>
                    <td><?= htmlspecialchars($contract['full_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <th>Giới tính</th>
                    <td><?= htmlspecialchars($genderLabels[$gender] ?? $gender) ?></td>
                    <th>Khoa/Viện</th>
                    <td><?= htmlspecialchars($contract['faculty'] ?? '-') ?></td>
                </tr>
                </tbody>
            </table>
        </section>

        <section class="contract-section">
            <h2>2. Thông tin phòng ở</h2>
            <table class="contract-info-table">
                <tbody>
                <tr>
                    <th>Tòa nhà</th>
                    <td><?= htmlspecialchars($contract['building_name'] ?? '-') ?></td>
                    <th>Phòng</th>
                    <td><?= htmlspecialchars($contract['room_number'] ?? '-') ?></td>
                </tr>
                <tr>
                    <th>Loại phòng</th>
                    <td><?= htmlspecialchars($roomTypeLabels[$roomType] ?? $roomType) ?></td>
                    <th>Khu vực</th>
                    <td><?= htmlspecialchars($genderLabels[$roomGender] ?? $roomGender) ?></td>
                </tr>
                <tr>
                    <th>Sức chứa</th>
                    <td><?= htmlspecialchars((string) ($contract['capacity'] ?? '-')) ?> sinh viên</td>
                    <th>Học kỳ</th>
                    <td>
                        <?= htmlspecialchars($contract['semester_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($contract['academic_year'] ?? '-') ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </section>

        <section class="contract-section">
            <h2>3. Thời hạn và tài chính</h2>
            <table class="contract-info-table">
                <tbody>
                <tr>
                    <th>Ngày bắt đầu</th>
                    <td><?= htmlspecialchars($contract['start_date'] ?? '-') ?></td>
                    <th>Ngày kết thúc</th>
                    <td><?= htmlspecialchars($contract['end_date'] ?? '-') ?></td>
                </tr>
                <tr>
                    <th>Giá hàng tháng</th>
                    <td><?= number_format($monthlyPrice) ?> VND</td>
                    <th>Tiền cọc</th>
                    <td><?= number_format($depositAmount) ?> VND</td>
                </tr>
                </tbody>
            </table>
        </section>

        <section class="contract-section">
            <h2>4. Cam kết thực hiện</h2>
            <div class="contract-terms-box">
                <ol class="contract-terms-list">
                    <li>Sinh viên xác nhận thông tin trong hợp đồng là đúng và nhận phòng theo phân bổ của ký túc xá.</li>
                    <li>Sinh viên tuân thủ nội quy cư trú, giữ gìn tài sản chung và chịu trách nhiệm khi làm hư hỏng thiết bị trong phòng.</li>
                    <li>Sinh viên thanh toán tiền phòng, điện nước và các khoản phí phát sinh đúng hạn theo thông báo của hệ thống.</li>
                    <li>Quản lý ký túc xá có quyền xử lý vi phạm, chấm dứt hợp đồng theo quy định khi sinh viên vi phạm nghiêm trọng.</li>
                </ol>
            </div>
        </section>

        <section class="compact-signature-grid">
            <div>
                <strong>Sinh viên</strong>
                <span>Ký và ghi rõ họ tên</span>
            </div>
            <div>
                <strong>Quản lý ký túc xá</strong>
                <span>Ký và ghi rõ họ tên</span>
            </div>
        </section>
    </article>
</section>
