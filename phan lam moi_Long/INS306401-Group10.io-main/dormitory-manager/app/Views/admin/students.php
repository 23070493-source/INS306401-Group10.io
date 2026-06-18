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
        <h3>Total Students</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Active</h3>
        <strong><?= htmlspecialchars($summary['active']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Inactive</h3>
        <strong><?= htmlspecialchars($summary['inactive']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Missing Profile</h3>
        <strong><?= htmlspecialchars($summary['missing_profile']) ?></strong>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/student-store" class="form-card wide-form">
    <h2>Create Student Profile</h2>

    <div class="admin-form-grid">
        <div>
            <label>User Student</label>
            <select name="user_id" required>
                <option value="">-- Select Student User --</option>
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
            <label>Student Code</label>
            <input type="text" name="student_code" required placeholder="Ví dụ: 20260001">
        </div>

        <div>
            <label>Full Name</label>
            <input type="text" name="full_name" required placeholder="Nguyễn Văn A">
        </div>

        <div>
            <label>Gender</label>
            <select name="gender" required>
                <option value="male">male</option>
                <option value="female">female</option>
                <option value="other">other</option>
            </select>
        </div>

        <div>
            <label>Faculty</label>
            <input type="text" name="faculty" placeholder="Ví dụ: HEDSPI">
        </div>

        <div>
            <label>Program</label>
            <input type="text" name="program" placeholder="Ví dụ: K69-1S">
        </div>

        <div>
            <label>Priority Type</label>
            <input type="text" name="priority_type" placeholder="normal / scholarship / policy">
        </div>

        <div>
            <label>Status</label>
            <select name="status" required>
                <option value="active">active</option>
                <option value="inactive">inactive</option>
            </select>
        </div>
    </div>

    <button type="submit">Create Student Profile</button>
</form>

<h2>Student List</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/students">All</a>
    <a class="filter-link <?= $statusFilter === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/students&status=active">Active</a>
    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/students&status=inactive">Inactive</a>
</div>

<?php if (empty($students)): ?>
    <div class="alert error">Không có hồ sơ sinh viên nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Account</th>
            <th>Student Code</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Faculty</th>
            <th>Program</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Update</th>
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
                        <button type="submit" class="btn-pay">Update</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
