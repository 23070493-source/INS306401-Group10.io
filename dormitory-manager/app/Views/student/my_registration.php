<?php
$totalRegistrations = count($registrations ?? []);

$latestRegistration = null;
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;

foreach ($registrations ?? [] as $registration) {
    if ($latestRegistration === null) {
        $latestRegistration = $registration;
    }

    $status = $registration['status'] ?? '';

    if ($status === 'pending') {
        $pendingCount++;
    } elseif ($status === 'approved') {
        $approvedCount++;
    } elseif ($status === 'rejected') {
        $rejectedCount++;
    }
}
?>

<h1>My Registration</h1>

<?php if (!$student): ?>
    <div class="alert error">
        Student profile not found.
    </div>

<?php elseif (empty($registrations)): ?>
    <section class="student-registration-empty">
        <div class="empty-state">
            No room registration has been submitted yet.
        </div>

        <div class="page-actions">
            <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
                Register Room
            </a>
        </div>
    </section>

<?php else: ?>

    <section class="student-registration-hero">
        <div>
            <span class="student-page-label">Registration Overview</span>
            <h2>Track your dormitory room registration</h2>
        </div>

        <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
            Register Room
        </a>
    </section>

    <section class="student-registration-summary-grid">
        <div class="student-registration-summary-card">
            <span>Total Registrations</span>
            <strong><?= htmlspecialchars((string) $totalRegistrations) ?></strong>
        </div>

        <div class="student-registration-summary-card">
            <span>Pending</span>
            <strong><?= htmlspecialchars((string) $pendingCount) ?></strong>
        </div>

        <div class="student-registration-summary-card">
            <span>Approved</span>
            <strong><?= htmlspecialchars((string) $approvedCount) ?></strong>
        </div>

        <div class="student-registration-summary-card">
            <span>Rejected</span>
            <strong><?= htmlspecialchars((string) $rejectedCount) ?></strong>
        </div>
    </section>

    <?php if ($latestRegistration): ?>
        <section class="student-registration-latest">
            <div class="student-result-header">
                <div>
                    <span>Latest Registration</span>
                    <h2>
                        <?= htmlspecialchars($latestRegistration['semester_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($latestRegistration['academic_year'] ?? '-') ?>
                    </h2>
                </div>

                <span class="badge <?= htmlspecialchars($latestRegistration['status'] ?? 'pending') ?>">
                    <?= htmlspecialchars(ucfirst($latestRegistration['status'] ?? 'pending')) ?>
                </span>
            </div>

            <div class="student-registration-detail-grid">
                <div>
                    <span>Desired Building</span>
                    <strong><?= htmlspecialchars($latestRegistration['desired_building'] ?? 'Any') ?></strong>
                </div>

                <div>
                    <span>Desired Room Type</span>
                    <strong><?= htmlspecialchars(ucfirst($latestRegistration['desired_room_type'] ?? '-')) ?></strong>
                </div>

                <div>
                    <span>Gender Type</span>
                    <strong><?= htmlspecialchars(ucfirst($latestRegistration['desired_gender_type'] ?? '-')) ?></strong>
                </div>

                <div>
                    <span>Priority Score</span>
                    <strong><?= htmlspecialchars((string) ($latestRegistration['priority_score'] ?? 0)) ?></strong>
                </div>

                <div>
                    <span>Assigned Room</span>
                    <strong>
                        <?php if (!empty($latestRegistration['assigned_room'])): ?>
                            <?= htmlspecialchars($latestRegistration['assigned_building'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($latestRegistration['assigned_room'] ?? '-') ?>
                        <?php else: ?>
                            Not assigned
                        <?php endif; ?>
                    </strong>
                </div>

                <div>
                    <span>Created At</span>
                    <strong><?= htmlspecialchars($latestRegistration['created_at'] ?? '-') ?></strong>
                </div>
            </div>

            <?php if (($latestRegistration['status'] ?? '') === 'rejected' && !empty($latestRegistration['rejection_reason'])): ?>
                <div class="student-rejection-box">
                    <strong>Rejection Reason</strong>
                    <p><?= htmlspecialchars($latestRegistration['rejection_reason']) ?></p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="student-registration-list">
        <div class="student-section-header">
            <div>
                <h2>Registration History</h2>
            </div>
        </div>

        <div class="student-registration-card-list">
            <?php foreach ($registrations as $registration): ?>
                <article class="student-registration-card">
                    <div class="student-registration-card-header">
                        <div>
                            <span>Registration #<?= htmlspecialchars((string) ($registration['id'] ?? '-')) ?></span>
                            <h3>
                                <?= htmlspecialchars($registration['semester_name'] ?? '-') ?>
                                -
                                <?= htmlspecialchars($registration['academic_year'] ?? '-') ?>
                            </h3>
                        </div>

                        <span class="badge <?= htmlspecialchars($registration['status'] ?? 'pending') ?>">
                            <?= htmlspecialchars(ucfirst($registration['status'] ?? 'pending')) ?>
                        </span>
                    </div>

                    <div class="student-registration-card-grid">
                        <div>
                            <span>Desired Building</span>
                            <strong><?= htmlspecialchars($registration['desired_building'] ?? 'Any') ?></strong>
                        </div>

                        <div>
                            <span>Desired Type</span>
                            <strong><?= htmlspecialchars(ucfirst($registration['desired_room_type'] ?? '-')) ?></strong>
                        </div>

                        <div>
                            <span>Gender</span>
                            <strong><?= htmlspecialchars(ucfirst($registration['desired_gender_type'] ?? '-')) ?></strong>
                        </div>

                        <div>
                            <span>Assigned Room</span>
                            <strong>
                                <?php if (!empty($registration['assigned_room'])): ?>
                                    <?= htmlspecialchars($registration['assigned_building'] ?? '-') ?>
                                    -
                                    <?= htmlspecialchars($registration['assigned_room'] ?? '-') ?>
                                <?php else: ?>
                                    Not assigned
                                <?php endif; ?>
                            </strong>
                        </div>

                        <div>
                            <span>Priority</span>
                            <strong><?= htmlspecialchars((string) ($registration['priority_score'] ?? 0)) ?></strong>
                        </div>

                        <div>
                            <span>Processed By</span>
                            <strong><?= htmlspecialchars($registration['processed_by'] ?? '-') ?></strong>
                        </div>

                        <div>
                            <span>Created At</span>
                            <strong><?= htmlspecialchars($registration['created_at'] ?? '-') ?></strong>
                        </div>
                    </div>

                    <?php if (($registration['status'] ?? '') === 'rejected' && !empty($registration['rejection_reason'])): ?>
                        <div class="student-rejection-box">
                            <strong>Rejection Reason</strong>
                            <p><?= htmlspecialchars($registration['rejection_reason']) ?></p>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Registration Table</h2>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Semester</th>
                <th>Desired Building</th>
                <th>Desired Type</th>
                <th>Gender</th>
                <th>Assigned Room</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Processed By</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($registrations as $registration): ?>
                <tr>
                    <td><?= htmlspecialchars((string) ($registration['id'] ?? '-')) ?></td>
                    <td>
                        <?= htmlspecialchars($registration['semester_name'] ?? '-') ?>
                        <br>
                        <small><?= htmlspecialchars($registration['academic_year'] ?? '-') ?></small>
                    </td>
                    <td><?= htmlspecialchars($registration['desired_building'] ?? 'Any') ?></td>
                    <td><?= htmlspecialchars(ucfirst($registration['desired_room_type'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars(ucfirst($registration['desired_gender_type'] ?? '-')) ?></td>
                    <td>
                        <?php if (!empty($registration['assigned_room'])): ?>
                            <?= htmlspecialchars($registration['assigned_building'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($registration['assigned_room'] ?? '-') ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars((string) ($registration['priority_score'] ?? 0)) ?></td>
                    <td>
                        <span class="badge <?= htmlspecialchars($registration['status'] ?? 'pending') ?>">
                            <?= htmlspecialchars(ucfirst($registration['status'] ?? 'pending')) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($registration['processed_by'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($registration['created_at'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php endif; ?>