<?php
$unpaidInvoiceCount = count($unpaidInvoices ?? []);
$violationPoints = (int) ($violationPoints ?? 0);

$violationLevel = 'Normal';
$violationClass = 'normal';

if ($violationPoints >= 15) {
    $violationLevel = 'Critical';
    $violationClass = 'critical';
} elseif ($violationPoints >= 10) {
    $violationLevel = 'Serious';
    $violationClass = 'serious';
} elseif ($violationPoints >= 5) {
    $violationLevel = 'Warning';
    $violationClass = 'warning';
}

$registrationStatus = $registration['status'] ?? 'No registration';
$contractCode = $contract['contract_code'] ?? 'No active contract';
?>

<h1>Student Dashboard</h1>

<?php if (!$student): ?>
    <div class="alert error">
        Student profile not found.
    </div>
<?php else: ?>

    <section class="student-dashboard-hero">
        <div>
            <p class="student-dashboard-label">Student Portal</p>
            <h2><?= htmlspecialchars($student['full_name']) ?></h2>

            <div class="student-profile-grid">
                <div>
                    <span>Student Code</span>
                    <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Gender</span>
                    <strong><?= htmlspecialchars($student['gender'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Faculty</span>
                    <strong><?= htmlspecialchars($student['faculty'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Priority Type</span>
                    <strong><?= htmlspecialchars($student['priority_type'] ?? '-') ?></strong>
                </div>
            </div>
        </div>
    </section>

    <section class="student-status-grid">
        <div class="student-status-card">
            <span>Registration</span>
            <strong><?= htmlspecialchars(ucfirst($registrationStatus)) ?></strong>
            <a href="<?= BASE_URL ?>/index.php?route=student/my-registration">View Registration</a>
        </div>

        <div class="student-status-card">
            <span>Contract</span>
            <strong><?= htmlspecialchars($contractCode) ?></strong>
            <a href="<?= BASE_URL ?>/index.php?route=student/my-contract">View Contract</a>
        </div>

        <div class="student-status-card">
            <span>Unpaid Invoices</span>
            <strong><?= htmlspecialchars((string) $unpaidInvoiceCount) ?></strong>
            <a href="<?= BASE_URL ?>/index.php?route=student/my-invoices">View Invoices</a>
        </div>

        <div class="student-status-card <?= htmlspecialchars($violationClass) ?>">
            <span>Violation Points</span>
            <strong><?= htmlspecialchars((string) $violationPoints) ?></strong>
            <em><?= htmlspecialchars($violationLevel) ?></em>
        </div>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Current Room</h2>
            </div>

            <?php if ($contract): ?>
                <span class="badge active">Active</span>
            <?php else: ?>
                <span class="badge pending">Not assigned</span>
            <?php endif; ?>
        </div>

        <?php if ($contract): ?>
            <div class="student-room-card">
                <div>
                    <span>Building</span>
                    <strong><?= htmlspecialchars($contract['building_name'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Room</span>
                    <strong><?= htmlspecialchars($contract['room_number'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Start Date</span>
                    <strong><?= htmlspecialchars($contract['start_date'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>End Date</span>
                    <strong><?= htmlspecialchars($contract['end_date'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Monthly Price</span>
                    <strong><?= number_format((float) ($contract['monthly_price'] ?? 0)) ?> VND</strong>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                No active room contract found.
            </div>
        <?php endif; ?>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Recent Maintenance Requests</h2>
            </div>

            <a class="student-small-link" href="<?= BASE_URL ?>/index.php?route=student/maintenance">
                View All
            </a>
        </div>

        <?php if (empty($maintenanceRequests)): ?>
            <div class="empty-state">
                No maintenance requests yet.
            </div>
        <?php else: ?>
            <div class="student-maintenance-list">
                <?php foreach ($maintenanceRequests as $request): ?>
                    <?php
                    $requestTitle = $request['title'] ?? $request['issue_title'] ?? 'Maintenance Request';
                    $requestStatus = $request['status'] ?? '-';
                    ?>
                    <div class="student-maintenance-item">
                        <div>
                            <strong><?= htmlspecialchars($requestTitle) ?></strong>
                            <span><?= htmlspecialchars($request['request_date'] ?? '-') ?></span>
                        </div>

                        <span class="badge <?= htmlspecialchars($requestStatus) ?>">
                            <?= htmlspecialchars(ucfirst($requestStatus)) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

<?php endif; ?>