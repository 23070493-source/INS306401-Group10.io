<h1>Đơn đăng ký phòng</h1>
<p>Danh sách đơn đăng ký phòng của sinh viên.</p>

<?php if (empty($registrations)): ?>
    <div class="alert error">Chưa có đơn đăng ký nào.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Mã sinh viên</th>
            <th>Họ và tên</th>
            <th>Giới tính</th>
            <th>Diện ưu tiên</th>
            <th>Học kỳ</th>
            <th>Tòa mong muốn</th>
            <th>Loại phòng</th>
            <th>Điểm ưu tiên</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
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
                <td><?= htmlspecialchars($registration['desired_building'] ?? 'Bất kỳ') ?></td>
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
                        Chi tiết
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
