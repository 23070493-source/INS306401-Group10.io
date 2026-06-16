<h1>My Registration</h1>
<p>Theo dõi trạng thái các đơn đăng ký phòng KTX của bạn.</p>

<?php if (!$student): ?>
    <div class="alert error">Không tìm thấy hồ sơ sinh viên.</div>
<?php elseif (empty($registrations)): ?>
    <div class="alert error">
        Bạn chưa có đơn đăng ký phòng nào.
    </div>

    <div class="page-actions">
        <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
            Register Room
        </a>
    </div>
<?php else: ?>

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
                <td><?= htmlspecialchars($registration['id']) ?></td>
                <td>
                    <?= htmlspecialchars($registration['semester_name']) ?>
                    <br>
                    <small><?= htmlspecialchars($registration['academic_year']) ?></small>
                </td>
                <td><?= htmlspecialchars($registration['desired_building'] ?? 'Any') ?></td>
                <td><?= htmlspecialchars($registration['desired_room_type'] ?? '-') ?></td>
                <td><?= htmlspecialchars($registration['desired_gender_type'] ?? '-') ?></td>
                <td>
                    <?php if (!empty($registration['assigned_room'])): ?>
                        <?= htmlspecialchars($registration['assigned_building']) ?>
                        -
                        <?= htmlspecialchars($registration['assigned_room']) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($registration['priority_score']) ?></td>
                <td>
                    <span class="badge <?= htmlspecialchars($registration['status']) ?>">
                        <?= htmlspecialchars($registration['status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($registration['processed_by'] ?? '-') ?></td>
                <td><?= htmlspecialchars($registration['created_at']) ?></td>
            </tr>

            <?php if ($registration['status'] === 'rejected' && !empty($registration['rejection_reason'])): ?>
                <tr>
                    <td colspan="10">
                        <strong>Rejection reason:</strong>
                        <?= htmlspecialchars($registration['rejection_reason']) ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>