<div class="login-card">
    <h1>Dormitory Manager</h1>

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
            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
            required
            autofocus
        >

        <label>Password</label>
        <input 
            type="password" 
            name="password" 
            required
        >

        <button type="submit">Login</button>
    </form>

    <div class="auth-switch">
        <a href="<?= BASE_URL ?>/index.php?route=forgot-password">
            Forgot password?
        </a>
    </div>

    <div class="auth-switch">
        Do not have an account?
        <a href="<?= BASE_URL ?>/index.php?route=register">
            Create student account
        </a>
    </div>
</div>