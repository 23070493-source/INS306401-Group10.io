<h1>Đơn đăng ký phòng</h1>
<p>Danh sách đơn đăng ký phòng của sinh viên.</p>

<?php if (empty($registrations)): ?>
    <div class="alert error">Chưa có đơn đăng ký nào.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Student Code</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Priority Type</th>
            <th>Semester</th>
            <th>Desired Building</th>
            <th>Room Type</th>
            <th>Score</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registrations as $registration): ?>
            <tr>
                <td><?= htmlspecialchars($registration['id']) ?></td>
                <td><?= htmlspecialchars($registration['student_code']) ?></td>
                <td><?= htmlspecialchars($registration['full_name']) ?></td>
                <td><?= htmlspecialchars($registration['gender']) ?></td>
                <td><?= htmlspecialchars($registration['priority_type']) ?></td>
                <td>
                    <?= htmlspecialchars($registration['semester_name']) ?>
                    <br>
                    <small><?= htmlspecialchars($registration['academic_year']) ?></small>
                </td>
                <td><?= htmlspecialchars($registration['desired_building'] ?? 'Any') ?></td>
                <td><?= htmlspecialchars($registration['desired_room_type'] ?? '-') ?></td>
                <td><?= htmlspecialchars($registration['priority_score']) ?></td>
                <td>
                    <span class="badge <?= htmlspecialchars($registration['status']) ?>">
                        <?= htmlspecialchars($registration['status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($registration['created_at']) ?></td>
                <td>
                    <a 
                        class="btn-small" 
                        href="<?= BASE_URL ?>/index.php?route=manager/registration-detail&id=<?= htmlspecialchars($registration['id']) ?>"
                    >
                        Detail
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
