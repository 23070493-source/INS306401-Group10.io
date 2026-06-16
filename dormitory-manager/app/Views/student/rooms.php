<h1>Available Rooms</h1>
<p>Danh sách phòng còn chỗ và có thể đăng ký.</p>

<?php if (empty($rooms)): ?>
    <div class="alert error">Hiện tại không có phòng trống.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Building</th>
            <th>Room</th>
            <th>Type</th>
            <th>Gender</th>
            <th>Capacity</th>
            <th>Current</th>
            <th>Available Beds</th>
            <th>Price / Month</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?= htmlspecialchars($room['building_name']) ?></td>
                <td><?= htmlspecialchars($room['room_number']) ?></td>
                <td><?= htmlspecialchars($room['room_type']) ?></td>
                <td><?= htmlspecialchars($room['gender_type']) ?></td>
                <td><?= htmlspecialchars($room['capacity']) ?></td>
                <td><?= htmlspecialchars($room['current_occupancy']) ?></td>
                <td>
                    <span class="badge success">
                        <?= htmlspecialchars($room['available_beds']) ?>
                    </span>
                </td>
                <td><?= number_format($room['price_per_month']) ?> VND</td>
                <td>
                    <span class="badge">
                        <?= htmlspecialchars($room['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="page-actions">
        <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
            Register Room
        </a>
    </div>
<?php endif; ?>