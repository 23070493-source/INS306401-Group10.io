<?php
$canRegister = $canRegister ?? true;
$activeContract = $activeContract ?? null;
$currentRegistration = $currentRegistration ?? null;

$roomTypeLabels = [
    'standard' => 'Tiêu chuẩn',
    'premium' => 'Cao cấp',
];
$genderLabels = [
    'male' => 'Nam',
    'female' => 'Nữ',
    'mixed' => 'Linh hoạt',
    'nam' => 'Nam',
    'nu' => 'Nữ',
    'nữ' => 'Nữ',
];
$statusLabels = [
    'active' => 'Đang hoạt động',
    'pending' => 'Chờ xử lý',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối',
    'cancelled' => 'Đã hủy',
];
$label = static function (array $labels, ?string $value): string {
    $key = strtolower(trim((string) $value));
    return $labels[$key] ?? ($value ?: '-');
};
?>

<h1>Đăng ký phòng</h1>

<?php if (!empty($success)): ?>
    <div class="alert success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!$student): ?>
    <div class="alert error">
        Không tìm thấy hồ sơ sinh viên.
    </div>
<?php else: ?>

    <section class="student-register-profile">
        <div>
            <span>Sinh viên</span>
            <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        </div>

        <div class="student-register-profile-grid">
            <div>
                <span>Mã sinh viên</span>
                <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
            </div>

            <div>
                <span>Giới tính</span>
                <strong><?= htmlspecialchars($label($genderLabels, $student['gender'] ?? null)) ?></strong>
            </div>

            <div>
                <span>Diện ưu tiên</span>
                <strong><?= htmlspecialchars($student['priority_type'] ?? '-') ?></strong>
            </div>
        </div>
    </section>

    <?php if (!$canRegister): ?>

        <?php if (!empty($activeContract)): ?>
            <section class="student-register-result success-state">
                <div class="student-result-header">
                    <div>
                        <span>Trạng thái hiện tại</span>
                        <h2>Hợp đồng ký túc xá đang hiệu lực</h2>
                    </div>

                    <span class="badge active">
                        <?= htmlspecialchars($label($statusLabels, $activeContract['status'] ?? 'active')) ?>
                    </span>
                </div>

                <div class="student-result-grid">
                    <div>
                        <span>Mã hợp đồng</span>
                        <strong><?= htmlspecialchars($activeContract['contract_code'] ?? '-') ?></strong>
                    </div>

                    <div>
                        <span>Phòng hiện tại</span>
                        <strong>
                            <?= htmlspecialchars($activeContract['building_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($activeContract['room_number'] ?? '-') ?>
                        </strong>
                    </div>

                    <div>
                        <span>Loại phòng</span>
                        <strong>
                            <?= htmlspecialchars($label($roomTypeLabels, $activeContract['room_type'] ?? null)) ?>
                            /
                            <?= htmlspecialchars($label($genderLabels, $activeContract['gender_type'] ?? null)) ?>
                        </strong>
                    </div>

                    <div>
                        <span>Học kỳ</span>
                        <strong>
                            <?= htmlspecialchars($activeContract['semester_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($activeContract['academic_year'] ?? '-') ?>
                        </strong>
                    </div>

                    <div>
                        <span>Ngày bắt đầu</span>
                        <strong><?= htmlspecialchars($activeContract['start_date'] ?? '-') ?></strong>
                    </div>

                    <div>
                        <span>Ngày kết thúc</span>
                        <strong><?= htmlspecialchars($activeContract['end_date'] ?? '-') ?></strong>
                    </div>
                </div>

                <div class="student-register-actions">
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-contract" class="student-primary-link">
                        Xem hợp đồng của tôi
                    </a>
                </div>
            </section>

        <?php elseif (!empty($currentRegistration)): ?>
            <section class="student-register-result pending-state">
                <div class="student-result-header">
                    <div>
                        <span>Trạng thái hiện tại</span>
                        <h2>Đơn đăng ký phòng đã được gửi</h2>
                    </div>

                    <span class="badge <?= htmlspecialchars($currentRegistration['status'] ?? 'pending') ?>">
                        <?= htmlspecialchars($label($statusLabels, $currentRegistration['status'] ?? 'pending')) ?>
                    </span>
                </div>

                <div class="student-result-grid">
                    <div>
                        <span>Học kỳ</span>
                        <strong>
                            <?= htmlspecialchars($currentRegistration['semester_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($currentRegistration['academic_year'] ?? '-') ?>
                        </strong>
                    </div>

                    <div>
                        <span>Tòa mong muốn</span>
                        <strong><?= htmlspecialchars($currentRegistration['desired_building'] ?? 'Không chọn tòa cụ thể') ?></strong>
                    </div>

                    <div>
                        <span>Loại phòng mong muốn</span>
                        <strong><?= htmlspecialchars($label($roomTypeLabels, $currentRegistration['desired_room_type'] ?? null)) ?></strong>
                    </div>

                    <div>
                        <span>Giới tính phòng</span>
                        <strong><?= htmlspecialchars($label($genderLabels, $currentRegistration['desired_gender_type'] ?? null)) ?></strong>
                    </div>

                    <?php if (!empty($currentRegistration['assigned_room'])): ?>
                        <div>
                            <span>Phòng được xếp</span>
                            <strong>
                                <?= htmlspecialchars($currentRegistration['assigned_building'] ?? '-') ?>
                                -
                                <?= htmlspecialchars($currentRegistration['assigned_room'] ?? '-') ?>
                            </strong>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($currentRegistration['processed_by_username'])): ?>
                        <div>
                            <span>Người xử lý</span>
                            <strong><?= htmlspecialchars($currentRegistration['processed_by_username']) ?></strong>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($currentRegistration['processed_at'])): ?>
                        <div>
                            <span>Thời điểm xử lý</span>
                            <strong><?= htmlspecialchars($currentRegistration['processed_at']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="student-register-actions">
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-registration" class="student-primary-link">
                        Xem đơn đăng ký của tôi
                    </a>
                </div>
            </section>
        <?php endif; ?>

    <?php else: ?>

        <section class="student-register-form-section">
            <div class="student-section-header">
                <div>
                    <h2>Biểu mẫu đăng ký phòng</h2>
                </div>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/index.php?route=student/register-room" class="student-register-form">
                <div class="student-form-line">
                    <label>Học kỳ</label>
                    <select name="semester_id" required>
                        <option value="">Chọn học kỳ</option>
                        <?php foreach ($semesters as $semester): ?>
                            <option
                                value="<?= htmlspecialchars($semester['id']) ?>"
                                <?= (($old['semester_id'] ?? '') == $semester['id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($semester['semester_name']) ?>
                                -
                                <?= htmlspecialchars($semester['academic_year']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="student-form-line">
                    <label>Tòa mong muốn</label>
                    <select name="desired_building_id">
                        <option value="">Không chọn tòa cụ thể</option>
                        <?php foreach ($buildings as $building): ?>
                            <option
                                value="<?= htmlspecialchars($building['id']) ?>"
                                <?= (($old['desired_building_id'] ?? '') == $building['id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($building['building_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="student-form-line">
                    <label>Loại phòng mong muốn</label>
                    <select name="desired_room_type" required>
                        <option value="">Chọn loại phòng</option>
                        <option value="standard" <?= (($old['desired_room_type'] ?? '') === 'standard') ? 'selected' : '' ?>>
                            Tiêu chuẩn
                        </option>
                        <option value="premium" <?= (($old['desired_room_type'] ?? '') === 'premium') ? 'selected' : '' ?>>
                            Cao cấp
                        </option>
                    </select>
                </div>

                <div class="student-form-line">
                    <label>Giới tính phòng</label>
                    <input
                        type="text"
                        value="<?= htmlspecialchars($label($genderLabels, $student['gender'] ?? null)) ?>"
                        disabled
                    >
                </div>

                <div class="student-form-line student-form-line-textarea">
                    <label>Ghi chú</label>
                    <textarea
                        name="note"
                        rows="4"
                        placeholder="Nhập nhu cầu hoặc ghi chú thêm nếu có"
                    ><?= htmlspecialchars($old['note'] ?? '') ?></textarea>
                </div>

                <button type="submit">Gửi đăng ký</button>
            </form>
        </section>

    <?php endif; ?>

<?php endif; ?>
