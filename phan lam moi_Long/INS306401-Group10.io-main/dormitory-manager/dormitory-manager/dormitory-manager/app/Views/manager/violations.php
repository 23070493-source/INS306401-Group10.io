<h1>Biên bản vi phạm</h1>
<p>Quản lý tạo biên bản vi phạm. Điểm phạt được hệ thống tự động gán theo loại vi phạm. Nếu sinh viên vượt ngưỡng cảnh báo rất nghiêm trọng, quản lý có thể chấm dứt hợp đồng KTX.</p>

<?php
$criticalThreshold = $criticalThreshold ?? 15;
$violationTypeLabels = [
    'Late return' => 'Về muộn',
    'Noise disturbance' => 'Gây ồn',
    'Poor hygiene' => 'Vệ sinh kém',
    'Unauthorized room change' => 'Tự ý đổi phòng',
    'Unpaid fee' => 'Chưa thanh toán phí',
    'Damage to property' => 'Làm hư hỏng tài sản',
    'Smoking or alcohol violation' => 'Hút thuốc hoặc sử dụng rượu bia',
    'Other' => 'Khác',
    'noise' => 'Gây ồn',
    'damaged_property' => 'Làm hư hỏng tài sản',
    'hygiene' => 'Vệ sinh kém',
    'unauthorized_guest' => 'Khách không được phép'
];
$violationDescriptionLabels = [
    'Made loud noise after quiet hours.' => 'Gây ồn sau giờ yên tĩnh.',
    'Damaged study desk in the room.' => 'Làm hỏng bàn học trong phòng.',
    'Room hygiene did not meet dormitory standard.' => 'Vệ sinh phòng không đạt tiêu chuẩn KTX.',
    'Had an unauthorized guest after visiting hours.' => 'Có khách không được phép sau giờ thăm.'
];
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
        <h3>Tổng biên bản</h3>
        <strong><?= htmlspecialchars($summary['total_violations']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Sinh viên cảnh báo</h3>
        <strong><?= htmlspecialchars($summary['warning_students']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Nghiêm trọng</h3>
        <strong><?= htmlspecialchars($summary['serious_students']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Rất nghiêm trọng</h3>
        <strong><?= htmlspecialchars($summary['critical_students']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=manager/violation-store" 
    class="form-card wide-form"
>
    <h2>Tạo biên bản vi phạm</h2>

    <label>Sinh viên</label>
    <select name="student_id" required>
        <option value="">-- Chọn sinh viên --</option>
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

    <label>Loại vi phạm</label>
    <select name="violation_type" id="violation_type" required>
        <option value="">-- Chọn loại vi phạm --</option>

        <?php foreach ($violationPointRules as $type => $points): ?>
            <option 
                value="<?= htmlspecialchars($type) ?>"
                data-points="<?= htmlspecialchars($points === null ? '' : (string)$points) ?>"
                <?= ($old['violation_type'] ?? '') === $type ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($violationTypeLabels[$type] ?? $type) ?>
                <?php if ($points !== null): ?>
                    - <?= htmlspecialchars($points) ?> điểm
                <?php else: ?>
                    - điểm tùy chỉnh
                <?php endif; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Điểm phạt</label>
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

    <label>Ngày vi phạm</label>
    <input
        type="date"
        name="violation_date"
        required
        value="<?= htmlspecialchars($old['violation_date'] ?? date('Y-m-d')) ?>"
    >

    <label>Mô tả</label>
    <textarea
        name="description"
        rows="4"
        required
        placeholder="Mô tả chi tiết vi phạm"
    ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>

    <button type="submit">Tạo biên bản</button>
</form>

<div class="rule-box">
    <h2>Bảng điểm vi phạm</h2>
    <p>
        Hệ thống dùng bảng quy đổi này để tự động tính điểm phạt.
        Nếu tổng điểm vi phạm từ 
        <strong><?= htmlspecialchars($criticalThreshold) ?> điểm</strong>
        trở lên, sinh viên sẽ ở mức cảnh báo rất nghiêm trọng và quản lý có thể chấm dứt hợp đồng.
    </p>

    <table>
        <thead>
        <tr>
            <th>Loại vi phạm</th>
            <th>Điểm phạt</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($violationPointRules as $type => $points): ?>
            <tr>
                <td><?= htmlspecialchars($violationTypeLabels[$type] ?? $type) ?></td>
                <td>
                    <?php if ($points === null): ?>
                        <span class="badge pending">Tùy chỉnh</span>
                    <?php else: ?>
                        <span class="badge danger">
                            <?= htmlspecialchars($points) ?> điểm
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h2>Tổng hợp sinh viên cảnh báo</h2>

<?php if (empty($warningStudents)): ?>
    <div class="alert success">Hiện chưa có sinh viên nào ở mức cảnh báo.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Mã sinh viên</th>
            <th>Họ và tên</th>
            <th>Khoa/Viện</th>
            <th>Số lần vi phạm</th>
            <th>Tổng điểm</th>
            <th>Mức cảnh báo</th>
            <th>Hợp đồng hiệu lực</th>
            <th>Thao tác</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($warningStudents as $student): ?>
            <?php
            $points = (int) $student['total_points'];
            $level = 'Cảnh báo';
            $class = 'warning';

            if ($points >= $criticalThreshold) {
                $level = 'Cảnh báo rất nghiêm trọng';
                $class = 'critical';
            } elseif ($points >= 10) {
                $level = 'Cảnh báo nghiêm trọng';
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
                        <?= htmlspecialchars($student['total_points']) ?> điểm
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
                            <?= htmlspecialchars($student['active_contract_code'] ?? ('Hợp đồng #' . $student['active_contract_id'])) ?>
                        </strong>
                        <br>
                        <small>
                            <?= htmlspecialchars($student['building_name'] ?? '-') ?>
                            -
                            <?= htmlspecialchars($student['room_number'] ?? '-') ?>
                        </small>
                    <?php else: ?>
                        <span class="badge pending">Không có hợp đồng đang hiệu lực</span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($points >= $criticalThreshold && $hasActiveContract): ?>
                        <form 
                            method="POST" 
                            action="<?= BASE_URL ?>/index.php?route=manager/violation-terminate-contract"
                            onsubmit="return confirm('Sinh viên đã vượt ngưỡng cảnh báo rất nghiêm trọng. Bạn có chắc muốn chấm dứt hợp đồng KTX không?');"
                        >
                            <input 
                                type="hidden" 
                                name="student_id" 
                                value="<?= htmlspecialchars($student['id']) ?>"
                            >

                            <button type="submit" class="btn-reject-small">
                                Chấm dứt hợp đồng
                            </button>
                        </form>
                    <?php elseif ($points >= $criticalThreshold && !$hasActiveContract): ?>
                        <small>Đã ở mức rất nghiêm trọng nhưng không có hợp đồng đang hiệu lực.</small>
                    <?php else: ?>
                        <small>Chưa đủ ngưỡng.</small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Danh sách vi phạm</h2>

<?php if (empty($violations)): ?>
    <div class="alert error">Chưa có biên bản vi phạm nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Sinh viên</th>
            <th>Khoa/Viện</th>
            <th>Loại vi phạm</th>
            <th>Mô tả</th>
            <th>Điểm</th>
            <th>Ngày vi phạm</th>
            <th>Người ghi nhận</th>
            <th>Ngày tạo</th>
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

                <td><?= htmlspecialchars($violationTypeLabels[$violation['violation_type'] ?? ''] ?? ($violation['violation_type'] ?? '-')) ?></td>

                <td><?= htmlspecialchars($violationDescriptionLabels[$violation['description'] ?? ''] ?? ($violation['description'] ?? '-')) ?></td>

                <td>
                    <span class="badge danger">
                        <?= htmlspecialchars($violation['penalty_points']) ?> điểm
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
    const translate = function (text) {
        if (window.dmTranslate && window.dmCurrentLang) {
            return window.dmTranslate(text, window.dmCurrentLang());
        }

        return text;
    };

    function updatePenaltyInput() {
        const selectedOption = violationTypeSelect.options[violationTypeSelect.selectedIndex];
        const selectedType = selectedOption.value;
        const points = selectedOption.getAttribute('data-points');

        if (!selectedType) {
            penaltyInput.value = '';
            penaltyInput.readOnly = true;
            penaltyInput.placeholder = translate('Chọn loại vi phạm trước');
            penaltyNote.textContent = translate('Vui lòng chọn loại vi phạm để hệ thống tự xác định điểm phạt.');
            return;
        }

        if (selectedType === 'Other') {
            penaltyInput.readOnly = false;
            penaltyInput.value = '';
            penaltyInput.placeholder = translate('Nhập điểm phạt cho loại Khác');
            penaltyNote.textContent = translate('Loại Khác cho phép quản lý nhập điểm phạt thủ công trong khoảng 1 - 20.');
            return;
        }

        penaltyInput.value = points;
        penaltyInput.readOnly = true;
        penaltyInput.placeholder = '';
        penaltyNote.textContent = translate('Điểm phạt đã được hệ thống tự động gán theo loại vi phạm.');
    }

    violationTypeSelect.addEventListener('change', updatePenaltyInput);
    updatePenaltyInput();
});
</script>
