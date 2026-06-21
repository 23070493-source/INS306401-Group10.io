<?php
$totalContracts = count($contracts ?? []);
$activeContracts = 0;
$latestContract = null;

$roomTypeLabels = [
    'standard' => 'Tiêu chuẩn',
    'premium' => 'Cao cấp',
];
$genderLabels = [
    'male' => 'Nam',
    'female' => 'Nữ',
    'mixed' => 'Linh hoạt',
];
$statusLabels = [
    'active' => 'Đang hoạt động',
    'expired' => 'Hết hạn',
    'terminated' => 'Đã chấm dứt',
    'cancelled' => 'Đã hủy',
];
$label = static function (array $labels, ?string $value): string {
    $key = strtolower(trim((string) $value));
    return $labels[$key] ?? ($value ?: '-');
};

foreach ($contracts ?? [] as $contract) {
    if ($latestContract === null) {
        $latestContract = $contract;
    }

    if (($contract['status'] ?? '') === 'active') {
        $activeContracts++;
    }
}
?>

<h1>Hợp đồng của tôi</h1>

<?php if (!$student): ?>
    <div class="alert error">
        Không tìm thấy hồ sơ sinh viên.
    </div>

<?php elseif (empty($contracts)): ?>
    <section class="student-contract-empty">
        <div class="empty-state">
            Chưa có hợp đồng ký túc xá.
        </div>

        <div class="page-actions">
            <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
                Đăng ký phòng
            </a>
        </div>
    </section>

<?php else: ?>

    <section class="student-contract-hero">
        <div>
            <span class="student-page-label">Tổng quan hợp đồng</span>
            <h2><?= htmlspecialchars($student['full_name'] ?? '-') ?></h2>
        </div>

        <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/my-invoices">
            Xem hóa đơn
        </a>
    </section>

    <section class="student-contract-profile-grid">
        <div>
            <span>Mã sinh viên</span>
            <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
        </div>

        <div>
            <span>Giới tính</span>
            <strong><?= htmlspecialchars($label($genderLabels, $student['gender'] ?? null)) ?></strong>
        </div>

        <div>
            <span>Khoa/Viện</span>
            <strong><?= htmlspecialchars($student['faculty'] ?? '-') ?></strong>
        </div>

        <div>
            <span>Chương trình</span>
            <strong><?= htmlspecialchars($student['program'] ?? '-') ?></strong>
        </div>
    </section>

    <section class="student-contract-summary-grid">
        <div class="student-contract-summary-card">
            <span>Tổng hợp đồng</span>
            <strong><?= htmlspecialchars((string) $totalContracts) ?></strong>
        </div>

        <div class="student-contract-summary-card">
            <span>Đang hiệu lực</span>
            <strong><?= htmlspecialchars((string) $activeContracts) ?></strong>
        </div>

        <div class="student-contract-summary-card">
            <span>Hợp đồng mới nhất</span>
            <strong><?= htmlspecialchars($latestContract['contract_code'] ?? '-') ?></strong>
        </div>
    </section>

    <?php foreach ($contracts as $contract): ?>
        <section class="student-contract-card">
            <div class="student-contract-card-header">
                <div>
                    <span>Hợp đồng</span>
                    <h2><?= htmlspecialchars($contract['contract_code'] ?? '-') ?></h2>
                </div>

                <div class="student-contract-price">
                    <strong><?= number_format((float) ($contract['monthly_price'] ?? 0)) ?> VND</strong>
                    <span>mỗi tháng</span>
                </div>

                <span class="badge <?= htmlspecialchars($contract['status'] ?? 'active') ?>">
                    <?= htmlspecialchars($label($statusLabels, $contract['status'] ?? 'active')) ?>
                </span>

                <a
                    class="student-primary-link no-print"
                    href="<?= BASE_URL ?>/index.php?route=student/contract-print&contract_id=<?= htmlspecialchars((string) ($contract['id'] ?? '')) ?>"
                    data-i18n="print_contract"
                >
                    In hợp đồng
                </a>
            </div>

            <div class="student-contract-detail-grid">
                <div>
                    <span>Tòa nhà</span>
                    <strong><?= htmlspecialchars($contract['building_name'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Phòng</span>
                    <strong><?= htmlspecialchars($contract['room_number'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Loại phòng</span>
                    <strong><?= htmlspecialchars($label($roomTypeLabels, $contract['room_type'] ?? null)) ?></strong>
                </div>

                <div>
                    <span>Giới tính phòng</span>
                    <strong><?= htmlspecialchars($label($genderLabels, $contract['gender_type'] ?? null)) ?></strong>
                </div>

                <div>
                    <span>Sức chứa</span>
                    <strong><?= htmlspecialchars((string) ($contract['capacity'] ?? '-')) ?></strong>
                </div>

                <div>
                    <span>Học kỳ</span>
                    <strong>
                        <?= htmlspecialchars($contract['semester_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($contract['academic_year'] ?? '-') ?>
                    </strong>
                </div>

                <div>
                    <span>Ngày bắt đầu</span>
                    <strong><?= htmlspecialchars($contract['start_date'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Ngày kết thúc</span>
                    <strong><?= htmlspecialchars($contract['end_date'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Tiền đặt cọc</span>
                    <strong><?= number_format((float) ($contract['deposit_amount'] ?? 0)) ?> VND</strong>
                </div>

                <div>
                    <span>Người tạo</span>
                    <strong><?= htmlspecialchars($contract['created_by_username'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Ngày tạo</span>
                    <strong><?= htmlspecialchars($contract['created_at'] ?? '-') ?></strong>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

<?php endif; ?>
