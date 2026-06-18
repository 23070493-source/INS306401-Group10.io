<div class="login-card">
    <h1>Đặt lại mật khẩu</h1>
    <p class="auth-note">Nhập tên đăng nhập và email đã đăng ký để tạo mật khẩu mới.</p>

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

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=forgot-password">
        <label>Tên đăng nhập</label>
        <input 
            type="text" 
            name="username" 
            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
            required
            autofocus
        >

        <label>Email</label>
        <input 
            type="email" 
            name="email" 
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            required
        >

        <label>Mật khẩu mới</label>
        <input 
            type="password" 
            name="new_password" 
            required
            minlength="6"
        >

        <label>Xác nhận mật khẩu mới</label>
        <input 
            type="password" 
            name="confirm_password" 
            required
            minlength="6"
        >

        <button type="submit">Đặt lại mật khẩu</button>
    </form>

    <div class="auth-switch">
        Đã nhớ mật khẩu?
        <a href="<?= BASE_URL ?>/index.php?route=login">
            Quay lại đăng nhập
        </a>
    </div>
</div>
