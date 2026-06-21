<h1>Hồ sơ cá nhân</h1>
<p>Quản lý thông tin tài khoản cá nhân, avatar và mật khẩu.</p>

<?php
$avatar = $user['avatar'] ?? '';
$avatarUrl = $avatar !== '' ? BASE_URL . '/' . ltrim($avatar, '/') : null;
$studentProfile = $studentProfile ?? $student ?? null;
?>

<?php if (!empty($success)): ?>
    <div class="alert success"><?= htmlspecialchars($success) ?></div>
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
            <h3>Hồ sơ sinh viên</h3>
            <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($studentProfile['student_code'] ?? '-') ?></p>
            <p><strong>Họ và tên:</strong> <?= htmlspecialchars($studentProfile['full_name'] ?? '-') ?></p>
            <p><strong>Giới tính:</strong> <?= htmlspecialchars($studentProfile['gender'] ?? '-') ?></p>
            <p><strong>Khoa:</strong> <?= htmlspecialchars($studentProfile['faculty'] ?? '-') ?></p>
            <p><strong>Chương trình:</strong> <?= htmlspecialchars($studentProfile['program'] ?? '-') ?></p>
            <p><strong>Diện ưu tiên:</strong> <?= htmlspecialchars($studentProfile['priority_type'] ?? '-') ?></p>
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
            <h2>Cập nhật liên hệ và avatar</h2>

            <label>Email</label>
            <input
                type="email"
                name="email"
                required
                value="<?= htmlspecialchars($user['email'] ?? '') ?>"
            >

            <label>Số điện thoại</label>
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
            <small>Cho phép JPG, PNG, WEBP. Tối đa 2MB.</small>

            <button type="submit">Lưu hồ sơ</button>
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
            <h2>Đổi mật khẩu</h2>

            <label>Mật khẩu hiện tại</label>
            <input
                type="password"
                name="current_password"
                required
                placeholder="Nhập mật khẩu hiện tại"
            >

            <label>Mật khẩu mới</label>
            <input
                type="password"
                name="new_password"
                required
                placeholder="Nhập mật khẩu mới"
            >

            <label>Xác nhận mật khẩu mới</label>
            <input
                type="password"
                name="confirm_password"
                required
                placeholder="Nhập lại mật khẩu mới"
            >

            <button type="submit">Đổi mật khẩu</button>
        </form>
    </div>
</div>
