<h1>Quản lý tài khoản</h1>
<p>Admin quản lý toàn bộ tài khoản đăng nhập của hệ thống: Admin, Manager và Student.</p>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="cards">
    <div class="card">
        <h3>Tổng tài khoản</h3>
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

    <div class="card">
        <h3>Quản trị viên</h3>
        <strong><?= htmlspecialchars($summary['admins']) ?></strong>
    </div>

    <div class="card">
        <h3>Quản lý</h3>
        <strong><?= htmlspecialchars($summary['managers']) ?></strong>
    </div>

    <div class="card">
        <h3>Sinh viên</h3>
        <strong><?= htmlspecialchars($summary['students']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=admin/user-store" 
    class="form-card wide-form"
>
    <h2>Tạo tài khoản người dùng</h2>

    <div class="admin-form-grid">
        <div>
            <label>Tên đăng nhập</label>
            <input
                type="text"
                name="username"
                required
                value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                placeholder="Ví dụ: manager_c"
            >
        </div>

        <div>
            <label>Mật khẩu</label>
            <input
                type="text"
                name="password"
                required
                value="<?= htmlspecialchars($old['password'] ?? 'password') ?>"
                placeholder="Mật khẩu mặc định"
            >
        </div>

        <div>
            <label>Email</label>
            <input
                type="email"
                name="email"
                required
                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                placeholder="example@email.com"
            >
        </div>

        <div>
            <label>Số điện thoại</label>
            <input
                type="text"
                name="phone"
                value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                placeholder="Số điện thoại"
            >
        </div>

        <div>
            <label>Vai trò</label>
            <select name="role_id" required>
                <option value="">-- Chọn vai trò --</option>
                <?php foreach ($roles as $role): ?>
                    <option 
                        value="<?= htmlspecialchars($role['id']) ?>"
                        <?= (int)($old['role_id'] ?? 0) === (int)$role['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($role['role_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Trạng thái</label>
            <select name="status" required>
                <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
            </select>
        </div>
    </div>

    <button type="submit">Tạo tài khoản</button>
</form>

<h2>Danh sách tài khoản</h2>

<div class="filter-bar">
    <a class="filter-link <?= $roleFilter === '' && $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/users">
        Tất cả
    </a>

    <a class="filter-link <?= $roleFilter === 'Admin' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/users&role=Admin">
        Admin
    </a>

    <a class="filter-link <?= $roleFilter === 'Manager' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/users&role=Manager">
        Manager
    </a>

    <a class="filter-link <?= $roleFilter === 'Student' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/users&role=Student">
        Student
    </a>

    <a class="filter-link <?= $statusFilter === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/users&status=active">
        Đang hoạt động
    </a>

    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/users&status=inactive">
        Ngừng hoạt động
    </a>
</div>

<?php if (empty($users)): ?>
    <div class="alert error">Không có user nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tài khoản</th>
            <th>Vai trò</th>
            <th>Hồ sơ sinh viên</th>
            <th>Liên hệ</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($users as $userRow): ?>
            <tr>
                <td><?= htmlspecialchars($userRow['id']) ?></td>

                <td>
                    <strong><?= htmlspecialchars($userRow['username']) ?></strong>
                </td>

                <td>
                    <span class="badge <?= strtolower(htmlspecialchars($userRow['role_name'])) ?>">
                        <?= htmlspecialchars($userRow['role_name']) ?>
                    </span>
                </td>

                <td>
                    <?php if ($userRow['role_name'] === 'Student'): ?>
                        <?php if (!empty($userRow['student_code'])): ?>
                            <?= htmlspecialchars($userRow['student_code']) ?>
                            <br>
                            <small><?= htmlspecialchars($userRow['student_full_name'] ?? '-') ?></small>
                        <?php else: ?>
                            <span class="badge pending">Chưa có hồ sơ</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </td>

                <td>
                    <?= htmlspecialchars($userRow['email'] ?? '-') ?>
                    <br>
                    <small><?= htmlspecialchars($userRow['phone'] ?? '-') ?></small>
                </td>

                <td>
                    <span class="badge <?= htmlspecialchars($userRow['status']) ?>">
                        <?= htmlspecialchars($userRow['status']) ?>
                    </span>
                </td>

                <td><?= htmlspecialchars($userRow['created_at'] ?? '-') ?></td>

                <td>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/user-toggle-status" class="inline-form">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userRow['id']) ?>">
                        <button type="submit" class="btn-pay">
                            <?= $userRow['status'] === 'active' ? 'Ngừng kích hoạt' : 'Kích hoạt' ?>
                        </button>
                    </form>

                    <form 
                        method="POST" 
                        action="<?= BASE_URL ?>/index.php?route=admin/user-reset-password" 
                        class="inline-form" 
                        onsubmit="return confirm('Reset password về password?');"
                    >
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userRow['id']) ?>">
                        <button type="submit" class="btn-reject-small">
                            Đặt lại mật khẩu
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
