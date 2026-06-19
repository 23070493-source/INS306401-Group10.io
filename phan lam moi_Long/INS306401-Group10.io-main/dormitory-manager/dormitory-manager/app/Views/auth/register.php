<div class="register-card register-card-wide">
    <div class="auth-brand">
        <img src="<?= BASE_URL ?>/assets/img/vnu-is-logo.jpg" alt="VNU-IS" class="auth-brand-logo">
        <div>
            <h1>Đăng ký tài khoản sinh viên</h1>
            <span>VNU International School</span>
        </div>
    </div>
    <p class="auth-note">Tạo tài khoản để đăng ký phòng, xem hợp đồng, hóa đơn và gửi yêu cầu sửa chữa.</p>

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

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=register">
        <div class="auth-section">
            <h2 class="auth-section-title">Thông tin tài khoản</h2>

            <div class="auth-form-line">
                <label>Tên đăng nhập</label>
                <input 
                    type="text" 
                    name="username" 
                    value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                    required
                >
            </div>

            <div class="auth-form-line">
                <label>Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required
                >
            </div>

            <div class="auth-form-line">
                <label>Số điện thoại</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Mật khẩu</label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    minlength="6"
                >
            </div>

            <div class="auth-form-line">
                <label>Xác nhận mật khẩu</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    required
                    minlength="6"
                >
            </div>
        </div>

        <div class="auth-section">
            <h2 class="auth-section-title">Hồ sơ sinh viên</h2>

            <div class="auth-form-line">
                <label>Mã sinh viên</label>
                <input 
                    type="text" 
                    name="student_code" 
                    value="<?= htmlspecialchars($old['student_code'] ?? '') ?>"
                    required
                >
            </div>

            <div class="auth-form-line">
                <label>Họ và tên</label>
                <input 
                    type="text" 
                    name="full_name" 
                    value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                    required
                >
            </div>

            <div class="auth-form-line">
                <label>Giới tính</label>
                <select name="gender" required>
                    <option value="">Chọn giới tính</option>
                    <option value="male" <?= (($old['gender'] ?? '') === 'male') ? 'selected' : '' ?>>
                        Nam
                    </option>
                    <option value="female" <?= (($old['gender'] ?? '') === 'female') ? 'selected' : '' ?>>
                        Nữ
                    </option>
                </select>
            </div>

            <div class="auth-form-line">
                <label>Ngày sinh</label>
                <input 
                    type="date" 
                    name="dob" 
                    value="<?= htmlspecialchars($old['dob'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Khoa</label>
                <input 
                    type="text" 
                    name="faculty" 
                    value="<?= htmlspecialchars($old['faculty'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Chương trình học</label>
                <input 
                    type="text" 
                    name="program" 
                    value="<?= htmlspecialchars($old['program'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Diện ưu tiên</label>
                <select name="priority_type">
                    <option value="none" <?= (($old['priority_type'] ?? 'none') === 'none') ? 'selected' : '' ?>>
                        Không
                    </option>
                    <option value="freshman" <?= (($old['priority_type'] ?? '') === 'freshman') ? 'selected' : '' ?>>
                        Tân sinh viên
                    </option>
                    <option value="international" <?= (($old['priority_type'] ?? '') === 'international') ? 'selected' : '' ?>>
                        Sinh viên quốc tế
                    </option>
                    <option value="policy" <?= (($old['priority_type'] ?? '') === 'policy') ? 'selected' : '' ?>>
                        Diện chính sách
                    </option>
                    <option value="scholarship" <?= (($old['priority_type'] ?? '') === 'scholarship') ? 'selected' : '' ?>>
                        Học bổng
                    </option>
                </select>
            </div>

            <div class="auth-form-line auth-form-line-textarea">
                <label>Địa chỉ</label>
                <textarea 
                    name="address" 
                    rows="3"
                ><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit">Tạo tài khoản</button>
    </form>

    <div class="auth-switch">
        Đã có tài khoản?
        <a href="<?= BASE_URL ?>/index.php?route=login">
            Quay lại đăng nhập
        </a>
    </div>
</div>
