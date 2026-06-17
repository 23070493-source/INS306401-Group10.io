<div class="login-card">
    <h1>Reset Password</h1>
    <p class="auth-note">Enter your username and registered email to reset your password.</p>

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
        <label>Username</label>
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

        <label>New Password</label>
        <input 
            type="password" 
            name="new_password" 
            required
            minlength="6"
        >

        <label>Confirm New Password</label>
        <input 
            type="password" 
            name="confirm_password" 
            required
            minlength="6"
        >

        <button type="submit">Reset Password</button>
    </form>

    <div class="auth-switch">
        Remember your password?
        <a href="<?= BASE_URL ?>/index.php?route=login">
            Back to login
        </a>
    </div>
</div>