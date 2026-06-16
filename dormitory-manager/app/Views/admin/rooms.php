<h1>Rooms</h1>
<p>Admin quản lý phòng, loại phòng, sức chứa, giá phòng và trạng thái sử dụng.</p>

<div class="cards">
    <div class="card">
        <h3>Total Rooms</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Available</h3>
        <strong><?= htmlspecialchars($summary['available']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Maintenance</h3>
        <strong><?= htmlspecialchars($summary['maintenance']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Inactive</h3>
        <strong><?= htmlspecialchars($summary['inactive']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=admin/room-store" 
    class="form-card wide-form"
>
    <h2>Create Room</h2>

    <div class="admin-form-grid">
        <div>
            <label>Building</label>
            <select name="building_id" required>
                <option value="">-- Select building --</option>
                <?php foreach ($buildings as $building): ?>
                    <option value="<?= htmlspecialchars($building['id']) ?>">
                        <?= htmlspecialchars($building['building_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Room Number</label>
            <input type="text" name="room_number" required placeholder="Ví dụ: A101">
        </div>

        <div>
            <label>Room Type</label>
            <select name="room_type" required>
                <option value="">-- Select type --</option>
                <option value="single">single</option>
                <option value="double">double</option>
                <option value="quad">quad</option>
                <option value="six">six</option>
                <option value="eight">eight</option>
            </select>
        </div>

        <div>
            <label>Gender Type</label>
            <select name="gender_type" required>
                <option value="">-- Select gender --</option>
                <option value="male">male</option>
                <option value="female">female</option>
                <option value="mixed">mixed</option>
            </select>
        </div>

        <div>
            <label>Capacity</label>
            <input type="number" name="capacity" min="1" required placeholder="Ví dụ: 4">
        </div>

        <div>
            <label>Price Per Month</label>
            <input type="number" name="price_per_month" min="0" step="1000" required placeholder="Ví dụ: 800000">
        </div>

        <div>
            <label>Status</label>
            <select name="status" required>
                <option value="available">available</option>
                <option value="full">full</option>
                <option value="maintenance">maintenance</option>
                <option value="inactive">inactive</option>
            </select>
        </div>
    </div>

    <button type="submit">Create Room</button>
</form>

<h2>Room List</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms">
        All
    </a>

    <a class="filter-link <?= $statusFilter === 'available' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms&status=available">
        Available
    </a>

    <a class="filter-link <?= $statusFilter === 'maintenance' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms&status=maintenance">
        Maintenance
    </a>

    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/rooms&status=inactive">
        Inactive
    </a>
</div>

<?php if (empty($rooms)): ?>
    <div class="alert error">Không có phòng nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Building</th>
            <th>Room</th>
            <th>Type</th>
            <th>Gender</th>
            <th>Capacity</th>
            <th>Occupancy</th>
            <th>Price</th>
            <th>Status</th>
            <th>Update</th>
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
                        <button type="submit" class="btn-pay">Update</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>