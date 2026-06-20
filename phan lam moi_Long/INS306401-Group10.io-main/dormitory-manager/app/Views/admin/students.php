<h1>Quản lý sinh viên</h1>
<p>Admin quản lý hồ sơ sinh viên. User role Student cần có student profile để sử dụng đầy đủ các flow.</p>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="cards">
    <div class="card">
        <h3>Tổng sinh viên</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Đang hoạt động</h3>
        <strong><?= htmlspecialchars($summary['active']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Ngừng hoạt động</h3>
        <strong><?= htmlspecialchars($summary['inactive']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Thiếu hồ sơ</h3>
        <strong><?= htmlspecialchars($summary['missing_profile']) ?></strong>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/student-store" class="form-card wide-form">
    <h2>Tạo hồ sơ sinh viên</h2>

    <div class="admin-form-grid">
        <div>
            <label>Tài khoản sinh viên</label>
            <select name="user_id" required>
                <option value="">-- Chọn tài khoản sinh viên --</option>
                <?php foreach ($studentUsers as $studentUser): ?>
                    <option value="<?= htmlspecialchars($studentUser['id']) ?>">
                        <?= htmlspecialchars($studentUser['username']) ?>
                        -
                        <?= htmlspecialchars($studentUser['email'] ?? '-') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Mã sinh viên</label>
            <input type="text" name="student_code" required placeholder="Ví dụ: 20260001">
        </div>

        <div>
            <label>Họ và tên</label>
            <input type="text" name="full_name" required placeholder="Nguyễn Văn A">
        </div>

        <div>
            <label>Giới tính</label>
            <select name="gender" required>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="other">Khác</option>
            </select>
        </div>

        <div>
            <label>Khoa/Viện</label>
            <input type="text" name="faculty" placeholder="Ví dụ: HEDSPI">
        </div>

        <div>
            <label>Chương trình</label>
            <input type="text" name="program" placeholder="Ví dụ: K69-1S">
        </div>

        <div>
            <label>Diện ưu tiên</label>
            <input type="text" name="priority_type" placeholder="normal / scholarship / policy">
        </div>

        <div>
            <label>Trạng thái</label>
            <select name="status" required>
                <option value="active">Đang hoạt động</option>
                <option value="inactive">Ngừng hoạt động</option>
            </select>
        </div>
    </div>

    <button type="submit">Tạo hồ sơ sinh viên</button>
</form>

<h2>Danh sách sinh viên</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/students">Tất cả</a>
    <a class="filter-link <?= $statusFilter === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/students&status=active">Đang hoạt động</a>
    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/students&status=inactive">Ngừng hoạt động</a>
</div>

<?php if (empty($students)): ?>
    <div class="alert error">Không có hồ sơ sinh viên nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tài khoản</th>
            <th>Mã sinh viên</th>
            <th>Họ và tên</th>
            <th>Giới tính</th>
            <th>Khoa/Viện</th>
            <th>Chương trình</th>
            <th>Ưu tiên</th>
            <th>Trạng thái</th>
            <th>Cập nhật</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/student-update">
                    <td>
                        <?= htmlspecialchars($student['id']) ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
                    </td>

                    <td>
                        <strong><?= htmlspecialchars($student['username'] ?? '-') ?></strong>
                        <br>
                        <small><?= htmlspecialchars($student['email'] ?? '-') ?></small>
                    </td>

                    <td>
                        <input class="table-input" type="text" name="student_code" value="<?= htmlspecialchars($student['student_code']) ?>" required>
                    </td>

                    <td>
                        <input class="table-input" type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
                    </td>

                    <td>
                        <select class="table-input" name="gender" required>
                            <?php foreach (['male', 'female', 'other'] as $gender): ?>
                                <option value="<?= $gender ?>" <?= $student['gender'] === $gender ? 'selected' : '' ?>>
                                    <?= $gender ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <input class="table-input" type="text" name="faculty" value="<?= htmlspecialchars($student['faculty'] ?? '') ?>">
                    </td>

                    <td>
                        <input class="table-input" type="text" name="program" value="<?= htmlspecialchars($student['program'] ?? '') ?>">
                    </td>

                    <td>
                        <input class="table-input" type="text" name="priority_type" value="<?= htmlspecialchars($student['priority_type'] ?? '') ?>">
                    </td>

                    <td>
                        <select class="table-input" name="status" required>
                            <option value="active" <?= $student['status'] === 'active' ? 'selected' : '' ?>>active</option>
                            <option value="inactive" <?= $student['status'] === 'inactive' ? 'selected' : '' ?>>inactive</option>
                        </select>
                    </td>

                    <td>
                        <button type="submit" class="btn-pay">Cập nhật</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
