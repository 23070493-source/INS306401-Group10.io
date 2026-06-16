<div class="login-card">
    <h1>Dormitory Manager</h1>
    <p>Hệ thống Quản lý KTX & Đăng ký Chỗ ở</p>

    <?php if (!empty($success)): ?>
        <div class="alert success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=login">
        <label>Username</label>
        <input 
            type="text" 
            name="username" 
            required 
            placeholder="admin01 / manager_a / student001"
        >

        <label>Password</label>
        <input 
            type="password" 
            name="password" 
            required 
            placeholder="password"
        >

        <button type="submit">Login</button>
    </form>

    <div class="auth-switch">
        Chưa có tài khoản sinh viên?
        <a href="<?= BASE_URL ?>/index.php?route=register">Đăng ký Student</a>
    </div>

    <div class="demo-accounts">
        <h3>Demo accounts</h3>
        <p><strong>Admin:</strong> admin01 / password</p>
        <p><strong>Manager:</strong> manager_a / password</p>
        <p><strong>Student:</strong> student001 / password</p>
    </div>
</div>