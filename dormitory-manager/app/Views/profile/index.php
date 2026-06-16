<h1>My Profile</h1>
<p>Quản lý thông tin tài khoản cá nhân, avatar và mật khẩu.</p>

<?php
$avatar = $user['avatar'] ?? '';
$avatarUrl = $avatar !== '' ? BASE_URL . '/' . ltrim($avatar, '/') : null;
?>

<?php if ($success === 'profile'): ?>
    <div class="alert success">Cập nhật thông tin cá nhân thành công.</div>
<?php elseif ($success === 'password'): ?>
    <div class="alert success">Đổi mật khẩu thành công.</div>
<?php endif; ?>

<div class="profile-layout">
    <div class="profile-card">
        <div class="profile-avatar-wrap">
            <?php if ($avatarUrl): ?>
                <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="profile-avatar">
            <?php else: ?>
                <div class="profile-avatar-placeholder">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

        <h2><?= htmlspecialchars($user['username']) ?></h2>

        <p>
            <span class="badge <?= strtolower(htmlspecialchars($user['role_name'])) ?>">
                <?= htmlspecialchars($user['role_name']) ?>
            </span>
        </p>

        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '-') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>

        <?php if ($user['role_name'] === 'Student' && $studentProfile): ?>
            <hr>
            <h3>Student Profile</h3>
            <p><strong>Student Code:</strong> <?= htmlspecialchars($studentProfile['student_code'] ?? '-') ?></p>
            <p><strong>Full Name:</strong> <?= htmlspecialchars($studentProfile['full_name'] ?? '-') ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($studentProfile['gender'] ?? '-') ?></p>
            <p><strong>Faculty:</strong> <?= htmlspecialchars($studentProfile['faculty'] ?? '-') ?></p>
            <p><strong>Program:</strong> <?= htmlspecialchars($studentProfile['program'] ?? '-') ?></p>
            <p><strong>Priority Type:</strong> <?= htmlspecialchars($studentProfile['priority_type'] ?? '-') ?></p>
        <?php endif; ?>
    </div>

    <div class="profile-forms">
        <?php if (!empty($profileErrors)): ?>
            <div class="alert error">
                <?php foreach ($profileErrors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form 
            method="POST" 
            action="<?= BASE_URL ?>/index.php?route=profile/update" 
            class="form-card"
            enctype="multipart/form-data"
        >
            <h2>Update Contact & Avatar</h2>

            <label>Email</label>
            <input
                type="email"
                name="email"
                required
                value="<?= htmlspecialchars($user['email'] ?? '') ?>"
            >

            <label>Phone</label>
            <input
                type="text"
                name="phone"
                value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
            >

            <label>Avatar</label>
            <input
                type="file"
                name="avatar"
                accept="image/jpeg,image/png,image/webp"
            >
            <small>Cho phép JPG, PNG, WEBP. Tối đa 5MB.</small>

            <button type="submit">Save Profile</button>
        </form>

        <?php if (!empty($passwordErrors)): ?>
            <div class="alert error">
                <?php foreach ($passwordErrors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form 
            method="POST" 
            action="<?= BASE_URL ?>/index.php?route=profile/password" 
            class="form-card"
        >
            <h2>Change Password</h2>

            <label>Current Password</label>
            <input
                type="password"
                name="current_password"
                required
                placeholder="Nhập mật khẩu hiện tại"
            >

            <label>New Password</label>
            <input
                type="password"
                name="new_password"
                required
                placeholder="Nhập mật khẩu mới"
            >

            <label>Confirm New Password</label>
            <input
                type="password"
                name="confirm_password"
                required
                placeholder="Nhập lại mật khẩu mới"
            >

            <button type="submit">Change Password</button>
        </form>
    </div>
</div>