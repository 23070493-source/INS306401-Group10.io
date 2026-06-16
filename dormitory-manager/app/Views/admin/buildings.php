<h1>Buildings</h1>
<p>Admin quản lý thông tin các tòa nhà trong ký túc xá.</p>

<div class="cards">
    <div class="card">
        <h3>Total Buildings</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Active</h3>
        <strong><?= htmlspecialchars($summary['active']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Inactive</h3>
        <strong><?= htmlspecialchars($summary['inactive']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=admin/building-store" 
    class="form-card wide-form"
>
    <h2>Create Building</h2>

    <div class="admin-form-grid">
        <div>
            <label>Building Name</label>
            <input
                type="text"
                name="building_name"
                required
                placeholder="Ví dụ: Building A"
            >
        </div>

        <div>
            <label>Address</label>
            <input
                type="text"
                name="address"
                placeholder="Ví dụ: Khu A - HUST Dormitory"
            >
        </div>

        <div>
            <label>Status</label>
            <select name="status" required>
                <option value="active">active</option>
                <option value="inactive">inactive</option>
            </select>
        </div>
    </div>

    <label>Description</label>
    <textarea name="description" rows="3" placeholder="Ghi chú về tòa nhà"></textarea>

    <button type="submit">Create Building</button>
</form>

<h2>Building List</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings">
        All
    </a>

    <a class="filter-link <?= $statusFilter === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings&status=active">
        Active
    </a>

    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings&status=inactive">
        Inactive
    </a>
</div>

<?php if (empty($buildings)): ?>
    <div class="alert error">Không có tòa nhà nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Building</th>
            <th>Address</th>
            <th>Rooms</th>
            <th>Status</th>
            <th>Description</th>
            <th>Created At</th>
            <th>Quick Update</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($buildings as $building): ?>
            <tr>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/building-update">
                    <td>
                        <?= htmlspecialchars($building['id']) ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($building['id']) ?>">
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="building_name" 
                            value="<?= htmlspecialchars($building['building_name']) ?>" 
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="address" 
                            value="<?= htmlspecialchars($building['address'] ?? '') ?>" 
                            class="table-input"
                        >
                    </td>

                    <td><?= htmlspecialchars($building['room_count']) ?></td>

                    <td>
                        <select name="status" class="table-input">
                            <option value="active" <?= $building['status'] === 'active' ? 'selected' : '' ?>>active</option>
                            <option value="inactive" <?= $building['status'] === 'inactive' ? 'selected' : '' ?>>inactive</option>
                        </select>
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="description" 
                            value="<?= htmlspecialchars($building['description'] ?? '') ?>" 
                            class="table-input"
                        >
                    </td>

                    <td><?= htmlspecialchars($building['created_at'] ?? '-') ?></td>

                    <td>
                        <button type="submit" class="btn-pay">Update</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>