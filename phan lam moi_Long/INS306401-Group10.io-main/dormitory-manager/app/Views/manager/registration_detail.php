<h1>Chi tiết đơn đăng ký</h1>
<p>Chi tiết đơn đăng ký và xử lý duyệt / từ chối.</p>

<div class="detail-grid">
    <div class="profile-box">
        <h2>Student Information</h2>
        <p><strong>Student Code:</strong> <?= htmlspecialchars($registration['student_code']) ?></p>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($registration['full_name']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($registration['gender']) ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($registration['dob'] ?? '-') ?></p>
        <p><strong>Faculty:</strong> <?= htmlspecialchars($registration['faculty'] ?? '-') ?></p>
        <p><strong>Program:</strong> <?= htmlspecialchars($registration['program'] ?? '-') ?></p>
        <p><strong>Priority Type:</strong> <?= htmlspecialchars($registration['priority_type']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($registration['address'] ?? '-') ?></p>
    </div>

    <div class="profile-box">
        <h2>Registration Information</h2>
        <p><strong>Registration ID:</strong> <?= htmlspecialchars($registration['id']) ?></p>
        <p>
            <strong>Semester:</strong>
            <?= htmlspecialchars($registration['semester_name']) ?>
            -
            <?= htmlspecialchars($registration['academic_year']) ?>
        </p>
        <p><strong>Desired Building:</strong> <?= htmlspecialchars($registration['desired_building'] ?? 'Any') ?></p>
        <p><strong>Desired Room Type:</strong> <?= htmlspecialchars($registration['desired_room_type'] ?? '-') ?></p>
        <p><strong>Desired Gender Type:</strong> <?= htmlspecialchars($registration['desired_gender_type'] ?? '-') ?></p>
        <p><strong>Priority Score:</strong> <?= htmlspecialchars($registration['priority_score']) ?></p>
        <p>
            <strong>Status:</strong>
            <span class="badge <?= htmlspecialchars($registration['status']) ?>">
                <?= htmlspecialchars($registration['status']) ?>
            </span>
        </p>
        <p><strong>Note:</strong> <?= htmlspecialchars($registration['note'] ?? '-') ?></p>

        <?php if ($registration['status'] === 'approved'): ?>
            <p>
                <strong>Assigned Room:</strong>
                <?= htmlspecialchars($registration['assigned_building'] ?? '-') ?>
                -
                <?= htmlspecialchars($registration['assigned_room'] ?? '-') ?>
            </p>
        <?php endif; ?>

        <?php if ($registration['status'] === 'rejected'): ?>
            <p><strong>Reject Reason:</strong> <?= htmlspecialchars($registration['rejection_reason'] ?? '-') ?></p>
        <?php endif; ?>
    </div>
</div>

<?php if ($registration['status'] === 'pending'): ?>

    <div class="section-heading">
        <div>
            <h2>Phòng phù hợp có thể xếp</h2>
            <p>Dữ liệu được lọc theo giới tính, loại phòng, tòa mong muốn và số giường còn trống.</p>
        </div>

        <button
            type="button"
            class="btn-secondary"
            data-fetch-room-suggestions
            data-registration-id="<?= htmlspecialchars($registration['id']) ?>"
            data-target-select="approval-room-select"
            data-target-table="room-suggestion-rows"
        >
            Tải lại gợi ý phòng
        </button>
    </div>

    <?php if (empty($availableRooms)): ?>
        <div class="alert error">
            Không có phòng phù hợp để duyệt đơn này.
        </div>
    <?php else: ?>
        <form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/registration-approve" class="form-card">
            <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['id']) ?>">

            <label>Chọn phòng</label>
            <select name="room_id" id="approval-room-select" required>
                <option value="">-- Chọn phòng --</option>

                <?php foreach ($availableRooms as $room): ?>
                    <option value="<?= htmlspecialchars($room['room_id']) ?>">
                        <?= htmlspecialchars($room['building_name']) ?>
                        -
                        Phòng <?= htmlspecialchars($room['room_number']) ?>
                        |
                        <?= htmlspecialchars($room['room_type']) ?>
                        |
                        <?= htmlspecialchars($room['gender_type']) ?>
                        |
                        Còn trống: <?= htmlspecialchars($room['available_beds']) ?> giường
                        |
                        <?= number_format($room['price_per_month']) ?> VND/month
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Duyệt và tạo hợp đồng</button>
        </form>

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
                <th>Price</th>
            </tr>
            </thead>
            <tbody id="room-suggestion-rows">
            <?php foreach ($availableRooms as $room): ?>
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
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Reject Registration</h2>

    <form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/registration-reject" class="form-card reject-form">
        <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['id']) ?>">

        <label>Rejection Reason</label>
        <textarea name="rejection_reason" rows="4" placeholder="Nhập lý do từ chối"></textarea>

        <button type="submit">Reject Registration</button>
    </form>

<?php else: ?>

    <div class="alert success">
        Đơn này đã được xử lý. Không thể duyệt hoặc từ chối lại.
    </div>

<?php endif; ?>

<div class="page-actions">
    <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=manager/registrations">
        Back to Registrations
    </a>
</div>
