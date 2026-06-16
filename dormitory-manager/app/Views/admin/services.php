<h1>Services</h1>
<p>Admin quản lý các dịch vụ tính phí như điện, nước, internet, vệ sinh, gửi xe.</p>

<div class="cards">
    <div class="card">
        <h3>Total Services</h3>
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
    action="<?= BASE_URL ?>/index.php?route=admin/service-store" 
    class="form-card wide-form"
>
    <h2>Create Service</h2>

    <div class="admin-form-grid">
        <div>
            <label>Service Name</label>
            <input type="text" name="service_name" required placeholder="Ví dụ: Electricity">
        </div>

        <div>
            <label>Unit</label>
            <input type="text" name="unit" placeholder="Ví dụ: kWh, m3, month">
        </div>

        <div>
            <label>Default Price</label>
            <input type="number" name="default_price" min="0" step="100" required placeholder="Ví dụ: 3500">
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
    <textarea name="description" rows="3" placeholder="Ghi chú về dịch vụ"></textarea>

    <button type="submit">Create Service</button>
</form>

<h2>Service List</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/services">
        All
    </a>

    <a class="filter-link <?= $statusFilter === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/services&status=active">
        Active
    </a>

    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/services&status=inactive">
        Inactive
    </a>
</div>

<?php if (empty($services)): ?>
    <div class="alert error">Không có dịch vụ nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Service Name</th>
            <th>Unit</th>
            <th>Default Price</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Update</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($services as $service): ?>
            <tr>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/service-update">
                    <td>
                        <?= htmlspecialchars($service['id']) ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($service['id']) ?>">
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="service_name" 
                            value="<?= htmlspecialchars($service['service_name']) ?>" 
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="unit" 
                            value="<?= htmlspecialchars($service['unit'] ?? '') ?>" 
                            class="table-input"
                        >
                    </td>

                    <td>
                        <input 
                            type="number" 
                            name="default_price" 
                            value="<?= htmlspecialchars($service['default_price'] ?? 0) ?>" 
                            min="0"
                            step="100"
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="description" 
                            value="<?= htmlspecialchars($service['description'] ?? '') ?>" 
                            class="table-input"
                        >
                    </td>

                    <td>
                        <select name="status" class="table-input">
                            <option value="active" <?= $service['status'] === 'active' ? 'selected' : '' ?>>active</option>
                            <option value="inactive" <?= $service['status'] === 'inactive' ? 'selected' : '' ?>>inactive</option>
                        </select>
                    </td>

                    <td><?= htmlspecialchars($service['created_at'] ?? '-') ?></td>

                    <td>
                        <button type="submit" class="btn-pay">Update</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>