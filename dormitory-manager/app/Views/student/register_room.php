<h1>Register Room</h1>
<p>Gửi đơn đăng ký phòng KTX theo học kỳ.</p>

<?php
$canRegister = $canRegister ?? true;
$activeContract = $activeContract ?? null;
$currentRegistration = $currentRegistration ?? null;
?>

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
    <div class="alert error">Không thể đăng ký vì chưa có hồ sơ sinh viên.</div>
<?php else: ?>

    <div class="profile-box">
        <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($student['student_code']) ?></p>
        <p><strong>Giới tính:</strong> <?= htmlspecialchars($student['gender']) ?></p>
        <p><strong>Diện ưu tiên:</strong> <?= htmlspecialchars($student['priority_type'] ?? '-') ?></p>
    </div>

    <?php if (!$canRegister): ?>

        <?php if (!empty($activeContract)): ?>
            <div class="alert success">
                <h2>Bạn đã có hợp đồng KTX đang hoạt động</h2>

                <p>Bạn không cần đăng ký phòng mới vì Manager đã duyệt đơn và hệ thống đã tạo hợp đồng cho bạn.</p>

                <p>
                    <strong>Mã hợp đồng:</strong>
                    <?= htmlspecialchars($activeContract['contract_code'] ?? '-') ?>
                </p>

                <p>
                    <strong>Phòng hiện tại:</strong>
                    <?= htmlspecialchars($activeContract['building_name'] ?? '-') ?>
                    -
                    <?= htmlspecialchars($activeContract['room_number'] ?? '-') ?>
                </p>

                <p>
                    <strong>Loại phòng:</strong>
                    <?= htmlspecialchars($activeContract['room_type'] ?? '-') ?>
                    /
                    <?= htmlspecialchars($activeContract['gender_type'] ?? '-') ?>
                </p>

                <p>
                    <strong>Học kỳ:</strong>
                    <?= htmlspecialchars($activeContract['semester_name'] ?? '-') ?>
                    -
                    <?= htmlspecialchars($activeContract['academic_year'] ?? '-') ?>
                </p>

                <p>
                    <strong>Thời gian:</strong>
                    <?= htmlspecialchars($activeContract['start_date'] ?? '-') ?>
                    →
                    <?= htmlspecialchars($activeContract['end_date'] ?? '-') ?>
                </p>

                <p>
                    <strong>Trạng thái:</strong>
                    <span class="badge active">
                        <?= htmlspecialchars($activeContract['status'] ?? 'active') ?>
                    </span>
                </p>

                <br>

                <a href="<?= BASE_URL ?>/index.php?route=student/my-contract" class="btn-pay">
                    View My Contract
                </a>
            </div>

        <?php elseif (!empty($currentRegistration)): ?>
            <div class="alert warning">
                <h2>Bạn đã có đơn đăng ký phòng</h2>

                <?php if ($currentRegistration['status'] === 'pending'): ?>
                    <p>
                        Đơn đăng ký của bạn đã được gửi thành công và đang chờ Manager duyệt.
                        Trong thời gian này, bạn không thể gửi thêm đơn đăng ký mới.
                    </p>
                <?php elseif ($currentRegistration['status'] === 'approved'): ?>
                    <p>
                        Đơn đăng ký của bạn đã được Manager chấp nhận.
                        Vui lòng kiểm tra hợp đồng KTX của bạn.
                    </p>
                <?php else: ?>
                    <p>Bạn đã có đơn đăng ký phòng trong hệ thống.</p>
                <?php endif; ?>

                <p>
                    <strong>Trạng thái:</strong>
                    <span class="badge <?= htmlspecialchars($currentRegistration['status']) ?>">
                        <?= htmlspecialchars($currentRegistration['status']) ?>
                    </span>
                </p>

                <p>
                    <strong>Học kỳ:</strong>
                    <?= htmlspecialchars($currentRegistration['semester_name'] ?? '-') ?>
                    -
                    <?= htmlspecialchars($currentRegistration['academic_year'] ?? '-') ?>
                </p>

                <p>
                    <strong>Tòa nhà mong muốn:</strong>
                    <?= htmlspecialchars($currentRegistration['desired_building'] ?? 'Không chọn cụ thể') ?>
                </p>

                <p>
                    <strong>Loại phòng mong muốn:</strong>
                    <?= htmlspecialchars($currentRegistration['desired_room_type'] ?? '-') ?>
                </p>

                <p>
                    <strong>Loại giới tính phòng:</strong>
                    <?= htmlspecialchars($currentRegistration['desired_gender_type'] ?? '-') ?>
                </p>

                <?php if (!empty($currentRegistration['assigned_room'])): ?>
                    <p>
                        <strong>Phòng được xếp:</strong>
                        <?= htmlspecialchars($currentRegistration['assigned_building'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($currentRegistration['assigned_room'] ?? '-') ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($currentRegistration['processed_by_username'])): ?>
                    <p>
                        <strong>Processed by:</strong>
                        <?= htmlspecialchars($currentRegistration['processed_by_username']) ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($currentRegistration['processed_at'])): ?>
                    <p>
                        <strong>Processed at:</strong>
                        <?= htmlspecialchars($currentRegistration['processed_at']) ?>
                    </p>
                <?php endif; ?>

                <br>

                <a href="<?= BASE_URL ?>/index.php?route=student/my-registration" class="btn-pay">
                    View My Registration
                </a>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?route=student/register-room" class="form-card">
            <label>Semester</label>
            <select name="semester_id" required>
                <option value="">-- Chọn học kỳ --</option>
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

            <label>Desired Building</label>
            <select name="desired_building_id">
                <option value="">-- Không chọn cụ thể --</option>
                <?php foreach ($buildings as $building): ?>
                    <option 
                        value="<?= htmlspecialchars($building['id']) ?>"
                        <?= (($old['desired_building_id'] ?? '') == $building['id']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($building['building_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Desired Room Type</label>
            <select name="desired_room_type" required>
                <option value="">-- Chọn loại phòng --</option>
                <option value="standard" <?= (($old['desired_room_type'] ?? '') === 'standard') ? 'selected' : '' ?>>
                    Standard
                </option>
                <option value="premium" <?= (($old['desired_room_type'] ?? '') === 'premium') ? 'selected' : '' ?>>
                    Premium
                </option>
            </select>

            <label>Desired Gender Type</label>
            <input 
                type="text" 
                value="<?= htmlspecialchars($student['gender']) ?>" 
                disabled
            >
            <small>Hệ thống tự lấy theo giới tính hồ sơ sinh viên.</small>

            <label>Note</label>
            <textarea 
                name="note" 
                rows="4" 
                placeholder="Ghi chú thêm nếu có"
            ><?= htmlspecialchars($old['note'] ?? '') ?></textarea>

            <button type="submit">Submit Registration</button>
        </form>

    <?php endif; ?>

<?php endif; ?>
