<h1>Biên bản vi phạm</h1>
<p>Manager tạo biên bản vi phạm. Điểm phạt được hệ thống tự động gán theo loại vi phạm. Nếu sinh viên vượt ngưỡng Critical Warning, Manager có thể chấm dứt hợp đồng KTX.</p>

<?php
$criticalThreshold = $criticalThreshold ?? 15;
?>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="cards">
    <div class="card">
        <h3>Total Violations</h3>
        <strong><?= htmlspecialchars($summary['total_violations']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Warning Students</h3>
        <strong><?= htmlspecialchars($summary['warning_students']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Serious</h3>
        <strong><?= htmlspecialchars($summary['serious_students']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Critical</h3>
        <strong><?= htmlspecialchars($summary['critical_students']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=manager/violation-store" 
    class="form-card wide-form"
>
    <h2>Create Violation Record</h2>

    <label>Student</label>
    <select name="student_id" required>
        <option value="">-- Select student --</option>
        <?php foreach ($students as $student): ?>
            <option 
                value="<?= htmlspecialchars($student['id']) ?>"
                <?= (int)($old['student_id'] ?? 0) === (int)$student['id'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($student['student_code']) ?>
                -
                <?= htmlspecialchars($student['full_name']) ?>
                -
                <?= htmlspecialchars($student['faculty'] ?? '-') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Violation Type</label>
    <select name="violation_type" id="violation_type" required>
        <option value="">-- Select violation type --</option>

        <?php foreach ($violationPointRules as $type => $points): ?>
            <option 
                value="<?= htmlspecialchars($type) ?>"
                data-points="<?= htmlspecialchars($points === null ? '' : (string)$points) ?>"
                <?= ($old['violation_type'] ?? '') === $type ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($type) ?>
                <?php if ($points !== null): ?>
                    - <?= htmlspecialchars($points) ?> points
                <?php else: ?>
                    - custom points
                <?php endif; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Penalty Points</label>
    <input
        type="number"
        id="custom_penalty_points"
        name="custom_penalty_points"
        min="1"
        max="20"
        value="<?= htmlspecialchars($old['custom_penalty_points'] ?? '') ?>"
        placeholder="Hệ thống tự điền theo loại vi phạm"
    >

    <small id="penalty_note">
        Với các loại vi phạm có sẵn, điểm phạt sẽ được hệ thống tự động điền và không cần nhập tay.
    </small>

    <label>Violation Date</label>
    <input
        type="date"
        name="violation_date"
        required
        value="<?= htmlspecialchars($old['violation_date'] ?? date('Y-m-d')) ?>"
    >

    <label>Description</label>
    <textarea
        name="description"
        rows="4"
        required
        placeholder="Mô tả chi tiết vi phạm"
    ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>

    <button type="submit">Create Violation</button>
</form>

<div class="rule-box">
    <h2>Violation Point Rules</h2>
    <p>
        Hệ thống dùng bảng quy đổi này để tự động tính điểm phạt.
        Nếu tổng điểm vi phạm từ 
        <strong><?= htmlspecialchars($criticalThreshold) ?> điểm</strong>
        trở lên, sinh viên sẽ ở mức Critical Warning và Manager có thể chấm dứt hợp đồng.
    </p>

    <table>
        <thead>
        <tr>
            <th>Violation Type</th>
            <th>Penalty Points</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($violationPointRules as $type => $points): ?>
            <tr>
                <td><?= htmlspecialchars($type) ?></td>
                <td>
                    <?php if ($points === null): ?>
                        <span class="badge pending">Custom</span>
                    <?php else: ?>
                        <span class="badge danger">
                            <?= htmlspecialchars($points) ?> points
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h2>Student Warning Summary</h2>

<?php if (empty($warningStudents)): ?>
    <div class="alert success">Hiện chưa có sinh viên nào ở mức cảnh báo.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Student Code</th>
            <th>Full Name</th>
            <th>Faculty</th>
            <th>Violation Count</th>
            <th>Total Points</th>
            <th>Warning Level</th>
            <th>Active Contract</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($warningStudents as $student): ?>
            <?php
            $points = (int) $student['total_points'];
            $level = 'Warning';
            $class = 'warning';

            if ($points >= $criticalThreshold) {
                $level = 'Critical Warning';
                $class = 'critical';
            } elseif ($points >= 10) {
                $level = 'Serious Warning';
                $class = 'serious';
            }

            $hasActiveContract = !empty($student['active_contract_id']);
            ?>

            <tr>
                <td><?= htmlspecialchars($student['student_code']) ?></td>

                <td><?= htmlspecialchars($student['full_name']) ?></td>

                <td><?= htmlspecialchars($student['faculty'] ?? '-') ?></td>

                <td><?= htmlspecialchars($student['violation_count']) ?></td>

                <td>
                    <span class="badge danger">
                        <?= htmlspecialchars($student['total_points']) ?> points
                    </span>
                </td>

                <td>
                    <span class="warning-label <?= htmlspecialchars($class) ?>">
                        <?= htmlspecialchars($level) ?>
                    </span>
                </td>

                <td>
                    <?php if ($hasActiveContract): ?>
                        <strong>
                            <?= htmlspecialchars($student['active_contract_code'] ?? ('Contract #' . $student['active_contract_id'])) ?>
                        </strong>
                        <br>
                        <small>
                            <?= htmlspecialchars($student['building_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($student['room_number'] ?? '-') ?>
                        </small>
                    <?php else: ?>
                        <span class="badge pending">No active contract</span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($points >= $criticalThreshold && $hasActiveContract): ?>
                        <form 
                            method="POST" 
                            action="<?= BASE_URL ?>/index.php?route=manager/violation-terminate-contract"
                            onsubmit="return confirm('Sinh viên đã vượt ngưỡng Critical Warning. Bạn có chắc muốn chấm dứt hợp đồng KTX không?');"
                        >
                            <input 
                                type="hidden" 
                                name="student_id" 
                                value="<?= htmlspecialchars($student['id']) ?>"
                            >

                            <button type="submit" class="btn-reject-small">
                                Terminate Contract
                            </button>
                        </form>
                    <?php elseif ($points >= $criticalThreshold && !$hasActiveContract): ?>
                        <small>Critical nhưng không có hợp đồng active.</small>
                    <?php else: ?>
                        <small>Chưa đủ ngưỡng.</small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Violation List</h2>

<?php if (empty($violations)): ?>
    <div class="alert error">Chưa có biên bản vi phạm nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Faculty</th>
            <th>Violation Type</th>
            <th>Description</th>
            <th>Points</th>
            <th>Violation Date</th>
            <th>Created By</th>
            <th>Created At</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($violations as $violation): ?>
            <tr>
                <td><?= htmlspecialchars($violation['id']) ?></td>

                <td>
                    <?= htmlspecialchars($violation['student_code']) ?>
                    <br>
                    <small><?= htmlspecialchars($violation['full_name']) ?></small>
                </td>

                <td><?= htmlspecialchars($violation['faculty'] ?? '-') ?></td>

                <td><?= htmlspecialchars($violation['violation_type'] ?? '-') ?></td>

                <td><?= htmlspecialchars($violation['description'] ?? '-') ?></td>

                <td>
                    <span class="badge danger">
                        <?= htmlspecialchars($violation['penalty_points']) ?> points
                    </span>
                </td>

                <td><?= htmlspecialchars($violation['violation_date'] ?? '-') ?></td>

                <td><?= htmlspecialchars($violation['created_by_username'] ?? '-') ?></td>

                <td><?= htmlspecialchars($violation['created_at'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const violationTypeSelect = document.getElementById('violation_type');
    const penaltyInput = document.getElementById('custom_penalty_points');
    const penaltyNote = document.getElementById('penalty_note');

    function updatePenaltyInput() {
        const selectedOption = violationTypeSelect.options[violationTypeSelect.selectedIndex];
        const selectedType = selectedOption.value;
        const points = selectedOption.getAttribute('data-points');

        if (!selectedType) {
            penaltyInput.value = '';
            penaltyInput.readOnly = true;
            penaltyInput.placeholder = 'Chọn loại vi phạm trước';
            penaltyNote.textContent = 'Vui lòng chọn loại vi phạm để hệ thống tự xác định điểm phạt.';
            return;
        }

        if (selectedType === 'Other') {
            penaltyInput.readOnly = false;
            penaltyInput.value = '';
            penaltyInput.placeholder = 'Nhập điểm phạt cho loại Other';
            penaltyNote.textContent = 'Loại Other cho phép Manager nhập điểm phạt thủ công trong khoảng 1 - 20.';
            return;
        }

        penaltyInput.value = points;
        penaltyInput.readOnly = true;
        penaltyInput.placeholder = '';
        penaltyNote.textContent = 'Điểm phạt đã được hệ thống tự động gán theo loại vi phạm.';
    }

    violationTypeSelect.addEventListener('change', updatePenaltyInput);
    updatePenaltyInput();
});
</script>
