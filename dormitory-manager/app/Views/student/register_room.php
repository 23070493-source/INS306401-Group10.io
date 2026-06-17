<?php
$canRegister = $canRegister ?? true;
$activeContract = $activeContract ?? null;
$currentRegistration = $currentRegistration ?? null;
?>

<h1>Register Room</h1>

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

<?php if (!$student): ?>
    <div class="alert error">
        Student profile not found.
    </div>
<?php else: ?>

    <section class="student-register-profile">
        <div>
            <span>Student</span>
            <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        </div>

        <div class="student-register-profile-grid">
            <div>
                <span>Student Code</span>
                <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
            </div>

            <div>
                <span>Gender</span>
                <strong><?= htmlspecialchars($student['gender'] ?? '-') ?></strong>
            </div>

            <div>
                <span>Priority Type</span>
                <strong><?= htmlspecialchars($student['priority_type'] ?? '-') ?></strong>
            </div>
        </div>
    </section>

    <?php if (!$canRegister): ?>

        <?php if (!empty($activeContract)): ?>
            <section class="student-register-result success-state">
                <div class="student-result-header">
                    <div>
                        <span>Current Status</span>
                        <h2>Active Dormitory Contract</h2>
                    </div>

                    <span class="badge active">
                        <?= htmlspecialchars(ucfirst($activeContract['status'] ?? 'active')) ?>
                    </span>
                </div>

                <div class="student-result-grid">
                    <div>
                        <span>Contract Code</span>
                        <strong><?= htmlspecialchars($activeContract['contract_code'] ?? '-') ?></strong>
                    </div>

                    <div>
                        <span>Current Room</span>
                        <strong>
                            <?= htmlspecialchars($activeContract['building_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($activeContract['room_number'] ?? '-') ?>
                        </strong>
                    </div>

                    <div>
                        <span>Room Type</span>
                        <strong>
                            <?= htmlspecialchars(ucfirst($activeContract['room_type'] ?? '-')) ?>
                            /
                            <?= htmlspecialchars(ucfirst($activeContract['gender_type'] ?? '-')) ?>
                        </strong>
                    </div>

                    <div>
                        <span>Semester</span>
                        <strong>
                            <?= htmlspecialchars($activeContract['semester_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($activeContract['academic_year'] ?? '-') ?>
                        </strong>
                    </div>

                    <div>
                        <span>Start Date</span>
                        <strong><?= htmlspecialchars($activeContract['start_date'] ?? '-') ?></strong>
                    </div>

                    <div>
                        <span>End Date</span>
                        <strong><?= htmlspecialchars($activeContract['end_date'] ?? '-') ?></strong>
                    </div>
                </div>

                <div class="student-register-actions">
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-contract" class="student-primary-link">
                        View My Contract
                    </a>
                </div>
            </section>

        <?php elseif (!empty($currentRegistration)): ?>
            <section class="student-register-result pending-state">
                <div class="student-result-header">
                    <div>
                        <span>Current Status</span>
                        <h2>Room Registration Submitted</h2>
                    </div>

                    <span class="badge <?= htmlspecialchars($currentRegistration['status'] ?? 'pending') ?>">
                        <?= htmlspecialchars(ucfirst($currentRegistration['status'] ?? 'pending')) ?>
                    </span>
                </div>

                <div class="student-result-grid">
                    <div>
                        <span>Semester</span>
                        <strong>
                            <?= htmlspecialchars($currentRegistration['semester_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($currentRegistration['academic_year'] ?? '-') ?>
                        </strong>
                    </div>

                    <div>
                        <span>Desired Building</span>
                        <strong><?= htmlspecialchars($currentRegistration['desired_building'] ?? 'No specific building') ?></strong>
                    </div>

                    <div>
                        <span>Desired Room Type</span>
                        <strong><?= htmlspecialchars(ucfirst($currentRegistration['desired_room_type'] ?? '-')) ?></strong>
                    </div>

                    <div>
                        <span>Desired Gender Type</span>
                        <strong><?= htmlspecialchars(ucfirst($currentRegistration['desired_gender_type'] ?? '-')) ?></strong>
                    </div>

                    <?php if (!empty($currentRegistration['assigned_room'])): ?>
                        <div>
                            <span>Assigned Room</span>
                            <strong>
                                <?= htmlspecialchars($currentRegistration['assigned_building'] ?? '-') ?>
                                -
                                <?= htmlspecialchars($currentRegistration['assigned_room'] ?? '-') ?>
                            </strong>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($currentRegistration['processed_by_username'])): ?>
                        <div>
                            <span>Processed By</span>
                            <strong><?= htmlspecialchars($currentRegistration['processed_by_username']) ?></strong>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($currentRegistration['processed_at'])): ?>
                        <div>
                            <span>Processed At</span>
                            <strong><?= htmlspecialchars($currentRegistration['processed_at']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="student-register-actions">
                    <a href="<?= BASE_URL ?>/index.php?route=student/my-registration" class="student-primary-link">
                        View My Registration
                    </a>
                </div>
            </section>
        <?php endif; ?>

    <?php else: ?>

        <section class="student-register-form-section">
            <div class="student-section-header">
                <div>
                    <h2>Room Registration Form</h2>
                </div>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/index.php?route=student/register-room" class="student-register-form">
                <div class="student-form-line">
                    <label>Semester</label>
                    <select name="semester_id" required>
                        <option value="">Select semester</option>
                        <?php foreach ($semesters as $semester): ?>
                            <option 
                                value="<?= htmlspecialchars($semester['id']) ?>"
                                <?= (($old['semester_id'] ?? '') == $semester['id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($semester['semester_name']) ?>
                                -
                                <?= htmlspecialchars($semester['academic_year']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="student-form-line">
                    <label>Desired Building</label>
                    <select name="desired_building_id">
                        <option value="">No specific building</option>
                        <?php foreach ($buildings as $building): ?>
                            <option 
                                value="<?= htmlspecialchars($building['id']) ?>"
                                <?= (($old['desired_building_id'] ?? '') == $building['id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($building['building_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="student-form-line">
                    <label>Desired Room Type</label>
                    <select name="desired_room_type" required>
                        <option value="">Select room type</option>
                        <option value="standard" <?= (($old['desired_room_type'] ?? '') === 'standard') ? 'selected' : '' ?>>
                            Standard
                        </option>
                        <option value="premium" <?= (($old['desired_room_type'] ?? '') === 'premium') ? 'selected' : '' ?>>
                            Premium
                        </option>
                    </select>
                </div>

                <div class="student-form-line">
                    <label>Gender Type</label>
                    <input 
                        type="text" 
                        value="<?= htmlspecialchars(ucfirst($student['gender'] ?? '-')) ?>" 
                        disabled
                    >
                </div>

                <div class="student-form-line student-form-line-textarea">
                    <label>Note</label>
                    <textarea 
                        name="note" 
                        rows="4"
                    ><?= htmlspecialchars($old['note'] ?? '') ?></textarea>
                </div>

                <button type="submit">Submit Registration</button>
            </form>
        </section>

    <?php endif; ?>

<?php endif; ?>