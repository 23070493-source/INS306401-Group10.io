<div class="login-card register-card">
    <h1>Student Registration</h1>
    <p>Tạo tài khoản sinh viên để đăng ký chỗ ở KTX.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=register">
        <h3>Account Information</h3>

        <label>Username</label>
        <input 
            type="text" 
            name="username" 
            required 
            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
            placeholder="Ví dụ: student009"
        >

        <label>Email</label>
        <input 
            type="email" 
            name="email" 
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            placeholder="student009@school.edu.vn"
        >

        <label>Phone</label>
        <input 
            type="text" 
            name="phone" 
            value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
            placeholder="09xxxxxxxx"
        >

        <label>Password</label>
        <input 
            type="password" 
            name="password" 
            required
        >

        <label>Confirm Password</label>
        <input 
            type="password" 
            name="confirm_password" 
            required
        >

        <h3>Student Profile</h3>

        <label>Student Code</label>
        <input 
            type="text" 
            name="student_code" 
            required 
            value="<?= htmlspecialchars($old['student_code'] ?? '') ?>"
            placeholder="Ví dụ: SV009"
        >

        <label>Full Name</label>
        <input 
            type="text" 
            name="full_name" 
            required 
            value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
            placeholder="Nguyen Van A"
        >

        <label>Gender</label>
        <select name="gender" required>
            <option value="">-- Select gender --</option>
            <option value="male" <?= (($old['gender'] ?? '') === 'male') ? 'selected' : '' ?>>
                Male
            </option>
            <option value="female" <?= (($old['gender'] ?? '') === 'female') ? 'selected' : '' ?>>
                Female
            </option>
            <option value="other" <?= (($old['gender'] ?? '') === 'other') ? 'selected' : '' ?>>
                Other
            </option>
        </select>

        <label>Date of Birth</label>
        <input 
            type="date" 
            name="dob" 
            value="<?= htmlspecialchars($old['dob'] ?? '') ?>"
        >

        <label>Faculty</label>
        <input 
            type="text" 
            name="faculty" 
            value="<?= htmlspecialchars($old['faculty'] ?? '') ?>"
            placeholder="Information Technology"
        >

        <label>Program</label>
        <input 
            type="text" 
            name="program" 
            value="<?= htmlspecialchars($old['program'] ?? '') ?>"
            placeholder="Software Engineering"
        >

        <label>Address</label>
        <input 
            type="text" 
            name="address" 
            value="<?= htmlspecialchars($old['address'] ?? '') ?>"
            placeholder="Ha Noi"
        >

        <button type="submit">Create Student Account</button>
    </form>

    <div class="auth-switch">
        Đã có tài khoản?
        <a href="<?= BASE_URL ?>/index.php?route=login">Quay lại đăng nhập</a>
    </div>
</div>