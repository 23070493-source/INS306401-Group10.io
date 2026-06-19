<div class="login-card">
    <div class="auth-brand">
        <img src="<?= BASE_URL ?>/assets/img/vnu-is-logo.jpg" alt="VNU-IS" class="auth-brand-logo">
        <div>
            <h1>Dormitory Manager</h1>
            <span>VNU International School</span>
        </div>
    </div>
    <p class="auth-note" data-i18n="login_intro">Đăng nhập vào hệ thống quản lý ký túc xá</p>

    <?php if (!empty($success)): ?>
        <div class="alert success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                alert(<?= json_encode($error, JSON_UNESCAPED_UNICODE) ?>);
            });
        </script>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=login">
        <label data-i18n="role">Vai trò</label>
        <select name="role_name" required>
            <option value="" data-i18n="choose_login_role">-- Chọn vai trò đăng nhập --</option>
            <option value="Admin" data-i18n="admin_role" <?= ($old['role_name'] ?? '') === 'Admin' ? 'selected' : '' ?>>Quản trị viên</option>
            <option value="Manager" data-i18n="manager_role_full" <?= ($old['role_name'] ?? '') === 'Manager' ? 'selected' : '' ?>>Quản lý ký túc xá</option>
            <option value="Student" data-i18n="student_role" <?= ($old['role_name'] ?? '') === 'Student' ? 'selected' : '' ?>>Sinh viên</option>
        </select>

        <label data-i18n="username">Tên đăng nhập</label>
        <input 
            type="text" 
            name="username" 
            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
            required
            autofocus
        >

        <label data-i18n="password">Mật khẩu</label>
        <input 
            type="password" 
            name="password" 
            required
        >

        <button type="submit" data-i18n="login">Đăng nhập</button>
    </form>

    <div class="auth-switch">
        <a href="<?= BASE_URL ?>/index.php?route=forgot-password">
            <span data-i18n="forgot_password">Quên mật khẩu?</span>
        </a>
    </div>

    <div class="auth-switch">
        <span data-i18n="no_student_account">Chưa có tài khoản sinh viên?</span>
        <a href="<?= BASE_URL ?>/index.php?route=register">
            <span data-i18n="create_account">Tạo tài khoản</span>
        </a>
    </div>
</div>
