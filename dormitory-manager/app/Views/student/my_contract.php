<?php
$totalContracts = count($contracts ?? []);
$activeContracts = 0;
$latestContract = null;

foreach ($contracts ?? [] as $contract) {
    if ($latestContract === null) {
        $latestContract = $contract;
    }

    if (($contract['status'] ?? '') === 'active') {
        $activeContracts++;
    }
}
?>

<h1>My Contract</h1>

<?php if (!$student): ?>
    <div class="alert error">
        Student profile not found.
    </div>

<?php elseif (empty($contracts)): ?>
    <section class="student-contract-empty">
        <div class="empty-state">
            No dormitory contract found.
        </div>

        <div class="page-actions">
            <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
                Register Room
            </a>
        </div>
    </section>

<?php else: ?>

    <section class="student-contract-hero">
        <div>
            <span class="student-page-label">Contract Overview</span>
            <h2><?= htmlspecialchars($student['full_name'] ?? '-') ?></h2>
        </div>

        <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/my-invoices">
            View Invoices
        </a>
    </section>

    <section class="student-contract-profile-grid">
        <div>
            <span>Student Code</span>
            <strong><?= htmlspecialchars($student['student_code'] ?? '-') ?></strong>
        </div>

        <div>
            <span>Gender</span>
            <strong><?= htmlspecialchars(ucfirst($student['gender'] ?? '-')) ?></strong>
        </div>

        <div>
            <span>Faculty</span>
            <strong><?= htmlspecialchars($student['faculty'] ?? '-') ?></strong>
        </div>

        <div>
            <span>Program</span>
            <strong><?= htmlspecialchars($student['program'] ?? '-') ?></strong>
        </div>
    </section>

    <section class="student-contract-summary-grid">
        <div class="student-contract-summary-card">
            <span>Total Contracts</span>
            <strong><?= htmlspecialchars((string) $totalContracts) ?></strong>
        </div>

        <div class="student-contract-summary-card">
            <span>Active Contracts</span>
            <strong><?= htmlspecialchars((string) $activeContracts) ?></strong>
        </div>

        <div class="student-contract-summary-card">
            <span>Latest Contract</span>
            <strong><?= htmlspecialchars($latestContract['contract_code'] ?? '-') ?></strong>
        </div>
    </section>

    <?php foreach ($contracts as $contract): ?>
        <section class="student-contract-card">
            <div class="student-contract-card-header">
                <div>
                    <span>Contract</span>
                    <h2><?= htmlspecialchars($contract['contract_code'] ?? '-') ?></h2>
                </div>

                <div class="student-contract-price">
                    <strong><?= number_format((float) ($contract['monthly_price'] ?? 0)) ?> VND</strong>
                    <span>per month</span>
                </div>

                <span class="badge <?= htmlspecialchars($contract['status'] ?? 'active') ?>">
                    <?= htmlspecialchars(ucfirst($contract['status'] ?? 'active')) ?>
                </span>
            </div>

            <div class="student-contract-detail-grid">
                <div>
                    <span>Building</span>
                    <strong><?= htmlspecialchars($contract['building_name'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Room</span>
                    <strong><?= htmlspecialchars($contract['room_number'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Room Type</span>
                    <strong><?= htmlspecialchars(ucfirst($contract['room_type'] ?? '-')) ?></strong>
                </div>

                <div>
                    <span>Gender Type</span>
                    <strong><?= htmlspecialchars(ucfirst($contract['gender_type'] ?? '-')) ?></strong>
                </div>

                <div>
                    <span>Capacity</span>
                    <strong><?= htmlspecialchars((string) ($contract['capacity'] ?? '-')) ?></strong>
                </div>

                <div>
                    <span>Semester</span>
                    <strong>
                        <?= htmlspecialchars($contract['semester_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($contract['academic_year'] ?? '-') ?>
                    </strong>
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
                    <span>Deposit Amount</span>
                    <strong><?= number_format((float) ($contract['deposit_amount'] ?? 0)) ?> VND</strong>
                </div>

                <div>
                    <span>Created By</span>
                    <strong><?= htmlspecialchars($contract['created_by_username'] ?? '-') ?></strong>
                </div>

                <div>
                    <span>Created At</span>
                    <strong><?= htmlspecialchars($contract['created_at'] ?? '-') ?></strong>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

<?php endif; ?>