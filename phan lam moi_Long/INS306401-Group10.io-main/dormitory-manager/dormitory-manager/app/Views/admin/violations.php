<h1 data-i18n="violations">Vi phạm</h1>
<p data-i18n="admin_violations_intro">Admin theo dõi toàn bộ biên bản vi phạm, điểm cảnh báo và người ghi nhận.</p>

<div class="cards">
    <div class="card">
        <h3 data-i18n="total_violations">Tổng biên bản</h3>
        <strong><?= htmlspecialchars((string) ($summary['total_violations'] ?? 0)) ?></strong>
    </div>

    <div class="card warning">
        <h3 data-i18n="warning_students">Sinh viên cảnh báo</h3>
        <strong><?= htmlspecialchars((string) ($summary['warning_students'] ?? 0)) ?></strong>
    </div>

    <div class="card danger">
        <h3 data-i18n="critical_students">Sinh viên critical</h3>
        <strong><?= htmlspecialchars((string) ($summary['critical_students'] ?? 0)) ?></strong>
    </div>

    <div class="card">
        <h3 data-i18n="total_penalty_points">Tổng điểm phạt</h3>
        <strong><?= htmlspecialchars((string) ($summary['total_points'] ?? 0)) ?></strong>
    </div>
</div>

<h2 data-i18n="warning_students">Sinh viên cảnh báo</h2>

<?php if (empty($warningStudents)): ?>
    <div class="alert success" data-i18n="no_violating_students">Chưa có sinh viên vi phạm.</div>
<?php else: ?>
    <div class="table-card">
        <table>
            <thead>
            <tr>
                <th data-i18n="student_code">Mã sinh viên</th>
                <th data-i18n="full_name">Họ và tên</th>
                <th data-i18n="penalty_points">Điểm phạt</th>
                <th data-i18n="violations">Vi phạm</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($warningStudents as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['student_code'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($student['full_name'] ?? '-') ?></td>
                    <td><span class="badge warning"><?= htmlspecialchars((string) ($student['total_points'] ?? 0)) ?></span></td>
                    <td><?= htmlspecialchars((string) ($student['violation_count'] ?? 0)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<h2 data-i18n="violation_records">Biên bản vi phạm</h2>

<?php if (empty($violations)): ?>
    <div class="alert success" data-i18n="no_violating_students">Chưa có sinh viên vi phạm.</div>
<?php else: ?>
    <div class="table-card">
        <table>
            <thead>
            <tr>
                <th data-i18n="student_code">Mã sinh viên</th>
                <th data-i18n="full_name">Họ và tên</th>
                <th data-i18n="room">Phòng</th>
                <th data-i18n="violation_type">Loại vi phạm</th>
                <th data-i18n="description">Mô tả</th>
                <th data-i18n="penalty_points">Điểm phạt</th>
                <th data-i18n="violation_date">Ngày vi phạm</th>
                <th data-i18n="recorded_by">Người ghi nhận</th>
                <th data-i18n="status">Trạng thái</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($violations as $violation): ?>
                <tr>
                    <td><?= htmlspecialchars($violation['student_code'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($violation['full_name'] ?? '-') ?></td>
                    <td>
                        <?= htmlspecialchars($violation['building_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($violation['room_number'] ?? '-') ?>
                    </td>
                    <td><?= htmlspecialchars($violation['violation_type'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($violation['description'] ?? '-') ?></td>
                    <td><span class="badge warning"><?= htmlspecialchars((string) ($violation['penalty_points'] ?? 0)) ?></span></td>
                    <td><?= htmlspecialchars($violation['violation_date'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($violation['recorded_by_username'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= htmlspecialchars($violation['status'] ?? 'recorded') ?>">
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $violation['status'] ?? 'recorded'))) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
