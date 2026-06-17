<div class="register-card register-card-wide">
    <h1>Student Registration</h1>
    <p class="auth-note">Create a student account to use the dormitory management system.</p>

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
            <h2 class="auth-section-title">Account Information</h2>

            <div class="auth-form-line">
                <label>Username</label>
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
                <label>Phone</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Password</label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    minlength="6"
                >
            </div>

            <div class="auth-form-line">
                <label>Confirm Password</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    required
                    minlength="6"
                >
            </div>
        </div>

        <div class="auth-section">
            <h2 class="auth-section-title">Student Profile</h2>

            <div class="auth-form-line">
                <label>Student Code</label>
                <input 
                    type="text" 
                    name="student_code" 
                    value="<?= htmlspecialchars($old['student_code'] ?? '') ?>"
                    required
                >
            </div>

            <div class="auth-form-line">
                <label>Full Name</label>
                <input 
                    type="text" 
                    name="full_name" 
                    value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                    required
                >
            </div>

            <div class="auth-form-line">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="">Select gender</option>
                    <option value="male" <?= (($old['gender'] ?? '') === 'male') ? 'selected' : '' ?>>
                        Male
                    </option>
                    <option value="female" <?= (($old['gender'] ?? '') === 'female') ? 'selected' : '' ?>>
                        Female
                    </option>
                </select>
            </div>

            <div class="auth-form-line">
                <label>Date of Birth</label>
                <input 
                    type="date" 
                    name="dob" 
                    value="<?= htmlspecialchars($old['dob'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Faculty</label>
                <input 
                    type="text" 
                    name="faculty" 
                    value="<?= htmlspecialchars($old['faculty'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Program</label>
                <input 
                    type="text" 
                    name="program" 
                    value="<?= htmlspecialchars($old['program'] ?? '') ?>"
                >
            </div>

            <div class="auth-form-line">
                <label>Priority Type</label>
                <select name="priority_type">
                    <option value="none" <?= (($old['priority_type'] ?? 'none') === 'none') ? 'selected' : '' ?>>
                        None
                    </option>
                    <option value="freshman" <?= (($old['priority_type'] ?? '') === 'freshman') ? 'selected' : '' ?>>
                        Freshman
                    </option>
                    <option value="international" <?= (($old['priority_type'] ?? '') === 'international') ? 'selected' : '' ?>>
                        International
                    </option>
                    <option value="policy" <?= (($old['priority_type'] ?? '') === 'policy') ? 'selected' : '' ?>>
                        Policy
                    </option>
                    <option value="scholarship" <?= (($old['priority_type'] ?? '') === 'scholarship') ? 'selected' : '' ?>>
                        Scholarship
                    </option>
                </select>
            </div>

            <div class="auth-form-line auth-form-line-textarea">
                <label>Address</label>
                <textarea 
                    name="address" 
                    rows="3"
                ><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit">Create Account</button>
    </form>

    <div class="auth-switch">
        Already have an account?
        <a href="<?= BASE_URL ?>/index.php?route=login">
            Back to login
        </a>
    </div>
</div>