<h1>Register Room</h1>
<p>Gửi đơn đăng ký phòng KTX theo học kỳ.</p>

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
        <p><strong>Diện ưu tiên:</strong> <?= htmlspecialchars($student['priority_type']) ?></p>
    </div>

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