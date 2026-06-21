<?php
$totalRegistrations = count($registrations ?? []);

$latestRegistration = null;
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;

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
    'pending' => 'Chờ xử lý',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối',
    'cancelled' => 'Đã hủy',
];
$label = static function (array $labels, ?string $value): string {
    $key = strtolower(trim((string) $value));
    return $labels[$key] ?? ($value ?: '-');
};

foreach ($registrations ?? [] as $registration) {
    if ($latestRegistration === null) {
        $latestRegistration = $registration;
    }

    $status = $registration['status'] ?? '';

    if ($status === 'pending') {
        $pendingCount++;
    } elseif ($status === 'approved') {
        $approvedCount++;
    } elseif ($status === 'rejected') {
        $rejectedCount++;
    }
}
?>

<h1>Đơn đăng ký của tôi</h1>

<?php if (!$student): ?>
    <div class="alert error">
        Không tìm thấy hồ sơ sinh viên.
    </div>

<?php elseif (empty($registrations)): ?>
    <section class="student-registration-empty">
        <div class="empty-state">
            Bạn chưa gửi đơn đăng ký phòng nào.
        </div>

        <div class="page-actions">
            <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
                Đăng ký phòng
            </a>
        </div>
    </section>

<?php else: ?>

    <section class="student-registration-hero">
        <div>
            <span class="student-page-label">Tổng quan đăng ký</span>
            <h2>Theo dõi hồ sơ đăng ký phòng ký túc xá</h2>
        </div>

        <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
            Đăng ký phòng
        </a>
    </section>

    <section class="student-registration-summary-grid">
        <div class="student-registration-summary-card">
            <span>Tổng số đơn</span>
            <strong><?= htmlspecialchars((string) $totalRegistrations) ?></strong>
        </div>

        <div class="student-registration-summary-card">
            <span>Chờ xử lý</span>
            <strong><?= htmlspecialchars((string) $pendingCount) ?></strong>
        </div>

        <div class="student-registration-summary-card">
            <span>Đã duyệt</span>
            <strong><?= htmlspecialchars((string) $approvedCount) ?></strong>
        </div>

        <div class="student-registration-summary-card">
            <span>Từ chối</span>
            <strong><?= htmlspecialchars((string) $rejectedCount) ?></strong>
        </div>
    </section>

    <?php if ($latestRegistration): ?>
        <section class="student-registration-latest">
            <div class="student-result-header">
                <div>
                    <span>Đơn mới nhất</span>
                    <h2>
                        <?= htmlspecialchars($latestRegistration['semester_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($latestRegistration['academic_year'] ?? '-') ?>
                    </h2>
                </div>

                <span class="badge <?= htmlspecialchars($latestRegistration['status'] ?? 'pending') ?>">
                    <?= htmlspecialchars($label($statusLabels, $latestRegistration['status'] ?? 'pending')) ?>
                </span>
            </div>

            <div class="student-registration-detail-grid">
                <div>
                    <span>Tòa mong muốn</span>
                    <strong><?= htmlspecialchars($latestRegistration['desired_building'] ?? 'Bất kỳ') ?></strong>
                </div>

                <div>
                    <span>Loại phòng mong muốn</span>
                    <strong><?= htmlspecialchars($label($roomTypeLabels, $latestRegistration['desired_room_type'] ?? null)) ?></strong>
                </div>

                <div>
                    <span>Giới tính phòng</span>
                    <strong><?= htmlspecialchars($label($genderLabels, $latestRegistration['desired_gender_type'] ?? null)) ?></strong>
                </div>

                <div>
                    <span>Mức ưu tiên</span>
                    <strong><?= htmlspecialchars((string) ($latestRegistration['priority_score'] ?? 0)) ?></strong>
                </div>

                <div>
                    <span>Phòng được xếp</span>
                    <strong>
                        <?php if (!empty($latestRegistration['assigned_room'])): ?>
                            <?= htmlspecialchars($latestRegistration['assigned_building'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($latestRegistration['assigned_room'] ?? '-') ?>
                        <?php else: ?>
                            Chưa xếp phòng
                        <?php endif; ?>
                    </strong>
                </div>

                <div>
                    <span>Ngày tạo</span>
                    <strong><?= htmlspecialchars($latestRegistration['created_at'] ?? '-') ?></strong>
                </div>
            </div>

            <?php if (($latestRegistration['status'] ?? '') === 'rejected' && !empty($latestRegistration['rejection_reason'])): ?>
                <div class="student-rejection-box">
                    <strong>Lý do từ chối</strong>
                    <p><?= htmlspecialchars($latestRegistration['rejection_reason']) ?></p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="student-registration-list">
        <div class="student-section-header">
            <div>
                <h2>Lịch sử đăng ký</h2>
            </div>
        </div>

        <div class="student-registration-card-list">
            <?php foreach ($registrations as $registration): ?>
                <article class="student-registration-card">
                    <div class="student-registration-card-header">
                        <div>
                            <span>Đơn đăng ký #<?= htmlspecialchars((string) ($registration['id'] ?? '-')) ?></span>
                            <h3>
                                <?= htmlspecialchars($registration['semester_name'] ?? '-') ?>
                                -
                                <?= htmlspecialchars($registration['academic_year'] ?? '-') ?>
                            </h3>
                        </div>

                        <span class="badge <?= htmlspecialchars($registration['status'] ?? 'pending') ?>">
                            <?= htmlspecialchars($label($statusLabels, $registration['status'] ?? 'pending')) ?>
                        </span>
                    </div>

                    <div class="student-registration-card-grid">
                        <div>
                            <span>Tòa mong muốn</span>
                            <strong><?= htmlspecialchars($registration['desired_building'] ?? 'Bất kỳ') ?></strong>
                        </div>

                        <div>
                            <span>Loại phòng mong muốn</span>
                            <strong><?= htmlspecialchars($label($roomTypeLabels, $registration['desired_room_type'] ?? null)) ?></strong>
                        </div>

                        <div>
                            <span>Giới tính</span>
                            <strong><?= htmlspecialchars($label($genderLabels, $registration['desired_gender_type'] ?? null)) ?></strong>
                        </div>

                        <div>
                            <span>Phòng được xếp</span>
                            <strong>
                                <?php if (!empty($registration['assigned_room'])): ?>
                                    <?= htmlspecialchars($registration['assigned_building'] ?? '-') ?>
                                    -
                                    <?= htmlspecialchars($registration['assigned_room'] ?? '-') ?>
                                <?php else: ?>
                                    Chưa xếp phòng
                                <?php endif; ?>
                            </strong>
                        </div>

                        <div>
                            <span>Mức ưu tiên</span>
                            <strong><?= htmlspecialchars((string) ($registration['priority_score'] ?? 0)) ?></strong>
                        </div>

                        <div>
                            <span>Người xử lý</span>
                            <strong><?= htmlspecialchars($registration['processed_by'] ?? '-') ?></strong>
                        </div>

                        <div>
                            <span>Ngày tạo</span>
                            <strong><?= htmlspecialchars($registration['created_at'] ?? '-') ?></strong>
                        </div>
                    </div>

                    <?php if (($registration['status'] ?? '') === 'rejected' && !empty($registration['rejection_reason'])): ?>
                        <div class="student-rejection-box">
                            <strong>Lý do từ chối</strong>
                            <p><?= htmlspecialchars($registration['rejection_reason']) ?></p>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Bảng đơn đăng ký</h2>
            </div>
        </div>

        <div class="student-table-scroll">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Học kỳ</th>
                    <th>Tòa mong muốn</th>
                    <th>Loại phòng</th>
                    <th>Giới tính</th>
                    <th>Phòng được xếp</th>
                    <th>Mức ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Người xử lý</th>
                    <th>Ngày tạo</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($registrations as $registration): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($registration['id'] ?? '-')) ?></td>
                        <td>
                            <?= htmlspecialchars($registration['semester_name'] ?? '-') ?>
                            <br>
                            <small><?= htmlspecialchars($registration['academic_year'] ?? '-') ?></small>
                        </td>
                        <td><?= htmlspecialchars($registration['desired_building'] ?? 'Bất kỳ') ?></td>
                        <td><?= htmlspecialchars($label($roomTypeLabels, $registration['desired_room_type'] ?? null)) ?></td>
                        <td><?= htmlspecialchars($label($genderLabels, $registration['desired_gender_type'] ?? null)) ?></td>
                        <td>
                            <?php if (!empty($registration['assigned_room'])): ?>
                                <?= htmlspecialchars($registration['assigned_building'] ?? '-') ?>
                                -
                                <?= htmlspecialchars($registration['assigned_room'] ?? '-') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars((string) ($registration['priority_score'] ?? 0)) ?></td>
                        <td>
                            <span class="badge <?= htmlspecialchars($registration['status'] ?? 'pending') ?>">
                                <?= htmlspecialchars($label($statusLabels, $registration['status'] ?? 'pending')) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($registration['processed_by'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($registration['created_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php endif; ?>
