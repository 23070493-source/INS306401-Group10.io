<h1>Phòng</h1>
<p>Admin quản lý phòng, loại phòng, sức chứa, giá phòng và trạng thái sử dụng.</p>

<div class="cards">
    <div class="card">
        <h3>Tổng số phòng</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Còn trống</h3>
        <strong><?= htmlspecialchars($summary['available']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Bảo trì</h3>
        <strong><?= htmlspecialchars($summary['maintenance']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Ngừng hoạt động</h3>
        <strong><?= htmlspecialchars($summary['inactive']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=admin/room-store" 
    class="form-card wide-form"
>
    <h2>Tạo phòng</h2>

    <div class="admin-form-grid">
        <div>
            <label>Tòa nhà</label>
            <select name="building_id" required>
                <option value="">-- Chọn tòa nhà --</option>
                <?php foreach ($buildings as $building): ?>
                    <option value="<?= htmlspecialchars($building['id']) ?>">
                        <?= htmlspecialchars($building['building_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Số phòng</label>
            <input type="text" name="room_number" required placeholder="Ví dụ: A101">
        </div>

        <div>
            <label>Loại phòng</label>
            <select name="room_type" required>
                <option value="">-- Chọn loại phòng --</option>
                <option value="single">Phòng đơn</option>
                <option value="double">Phòng đôi</option>
                <option value="quad">Phòng 4 người</option>
                <option value="six">Phòng 6 người</option>
                <option value="eight">Phòng 8 người</option>
            </select>
        </div>

        <div>
            <label>Giới tính phòng</label>
            <select name="gender_type" required>
                <option value="">-- Chọn giới tính --</option>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="mixed">Linh hoạt</option>
            </select>
        </div>

        <div>
            <label>Sức chứa</label>
            <input type="number" name="capacity" min="1" required placeholder="Ví dụ: 4">
        </div>

        <div>
            <label>Giá mỗi tháng</label>
            <input type="number" name="price_per_month" min="0" step="1000" required placeholder="Ví dụ: 800000">
        </div>

        <div>
            <label>Trạng thái</label>
            <select name="status" required>
                <option value="available">Còn trống</option>
                <option value="full">Đã đầy</option>
                <option value="maintenance">Bảo trì</option>
                <option value="inactive">Ngừng hoạt động</option>
            </select>
        </div>
    </div>

    <button type="submit">Tạo phòng</button>
</form>

<h2>Danh sách phòng</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
        Tất cả
    </a>

    <a class="filter-link <?= $statusFilter === 'available' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms&status=available">
        Còn trống
    </a>

    <a class="filter-link <?= $statusFilter === 'maintenance' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms&status=maintenance">
        Bảo trì
    </a>

    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms&status=inactive">
        Ngừng hoạt động
    </a>
</div>

<?php if (empty($rooms)): ?>
    <div class="alert error">Không có phòng nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tòa nhà</th>
            <th>Phòng</th>
            <th>Loại phòng</th>
            <th>Giới tính</th>
            <th>Sức chứa</th>
            <th>Đang ở</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Cập nhật</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/room-update">
                    <td>
                        <?= htmlspecialchars($room['id']) ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($room['id']) ?>">
                    </td>

                    <td>
                        <select name="building_id" class="table-input" required>
                            <?php foreach ($buildings as $building): ?>
                                <option 
                                    value="<?= htmlspecialchars($building['id']) ?>"
                                    <?= (int)$building['id'] === (int)$room['building_id'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($building['building_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="room_number" 
                            value="<?= htmlspecialchars($room['room_number']) ?>" 
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <select name="room_type" class="table-input" required>
                            <?php foreach (['single', 'double', 'quad', 'six', 'eight'] as $type): ?>
                                <option value="<?= $type ?>" <?= $room['room_type'] === $type ? 'selected' : '' ?>>
                                    <?= $type ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <select name="gender_type" class="table-input" required>
                            <?php foreach (['male', 'female', 'mixed'] as $gender): ?>
                                <option value="<?= $gender ?>" <?= $room['gender_type'] === $gender ? 'selected' : '' ?>>
                                    <?= $gender ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <input 
                            type="number" 
                            name="capacity" 
                            value="<?= htmlspecialchars($room['capacity']) ?>" 
                            min="1"
                            required
                            class="table-input small-table-input"
                        >
                    </td>

                    <td>
                        <?= htmlspecialchars($room['current_occupancy']) ?>
                        /
                        <?= htmlspecialchars($room['capacity']) ?>
                    </td>

                    <td>
                        <input 
                            type="number" 
                            name="price_per_month" 
                            value="<?= htmlspecialchars($room['price_per_month']) ?>" 
                            min="0"
                            step="1000"
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <select name="status" class="table-input">
                            <?php foreach (['available', 'full', 'maintenance', 'inactive'] as $status): ?>
                                <option value="<?= $status ?>" <?= $room['status'] === $status ? 'selected' : '' ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <button type="submit" class="btn-pay">Cập nhật</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
