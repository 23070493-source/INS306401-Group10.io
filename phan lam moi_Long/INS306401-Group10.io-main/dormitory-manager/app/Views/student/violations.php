<h1>Vi phạm của tôi</h1>
<p>Xem lịch sử vi phạm nội quy KTX và mức cảnh báo hiện tại.</p>

<?php if (!$student): ?>
    <div class="alert error">Không tìm thấy hồ sơ sinh viên.</div>

<?php else: ?>

    <?php
    $warningLevel = 'Bình thường';
    $warningClass = 'normal';
    $warningMessage = 'Bạn chưa nằm trong nhóm cảnh báo. Hãy tiếp tục tuân thủ nội quy KTX.';

    if ($totalPoints >= 15) {
        $warningLevel = 'Cảnh báo rất nghiêm trọng';
        $warningClass = 'critical';
        $warningMessage = 'Bạn đang ở mức cảnh báo rất nghiêm trọng. Có thể bị xem xét kỷ luật hoặc chấm dứt hợp đồng KTX.';
    } elseif ($totalPoints >= 10) {
        $warningLevel = 'Cảnh báo nghiêm trọng';
        $warningClass = 'serious';
        $warningMessage = 'Bạn đang ở mức cảnh báo nghiêm trọng. Cần chú ý tuân thủ nội quy để tránh bị xử lý kỷ luật.';
    } elseif ($totalPoints >= 5) {
        $warningLevel = 'Cảnh báo';
        $warningClass = 'warning';
        $warningMessage = 'Bạn đã có một số điểm vi phạm. Cần chú ý hơn trong sinh hoạt tại KTX.';
    }
    ?>

    <div class="profile-box">
        <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($student['student_code']) ?></p>
        <p><strong>Khoa/Viện:</strong> <?= htmlspecialchars($student['faculty'] ?? '-') ?></p>
    </div>

    <div class="violation-warning-box <?= htmlspecialchars($warningClass) ?>">
        <h2><?= htmlspecialchars($warningLevel) ?></h2>
        <p><strong>Tổng điểm vi phạm:</strong> <?= htmlspecialchars($totalPoints) ?></p>
        <p><?= htmlspecialchars($warningMessage) ?></p>
    </div>

    <h2>Lịch sử vi phạm</h2>

    <?php if (empty($violations)): ?>
        <div class="alert success">
            Bạn chưa có vi phạm nào.
        </div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Ngày vi phạm</th>
                <th>Loại vi phạm</th>
                <th>Mô tả</th>
                <th>Điểm phạt</th>
                <th>Người tạo</th>
                <th>Ngày tạo</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($violations as $violation): ?>
                <tr>
                    <td><?= htmlspecialchars($violation['id']) ?></td>
                    <td><?= htmlspecialchars($violation['violation_date'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($violation['violation_type'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($violation['description'] ?? '-') ?></td>
                    <td>
                        <span class="badge danger">
                            <?= htmlspecialchars($violation['penalty_points']) ?> điểm
                        </span>
                    </td>
                    <td><?= htmlspecialchars($violation['created_by_username'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($violation['created_at'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>
