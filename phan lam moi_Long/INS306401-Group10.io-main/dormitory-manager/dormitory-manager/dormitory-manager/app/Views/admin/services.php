<h1>Dịch vụ</h1>
<p>Admin quản lý các dịch vụ tính phí như điện, nước, internet, vệ sinh, gửi xe.</p>

<div class="cards">
    <div class="card">
        <h3>Tổng dịch vụ</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Đang hoạt động</h3>
        <strong><?= htmlspecialchars($summary['active']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Ngừng hoạt động</h3>
        <strong><?= htmlspecialchars($summary['inactive']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=admin/service-store" 
    class="form-card wide-form"
>
    <h2>Tạo dịch vụ</h2>

    <div class="admin-form-grid">
        <div>
            <label>Tên dịch vụ</label>
            <input type="text" name="service_name" required placeholder="Ví dụ: Electricity">
        </div>

        <div>
            <label>Đơn vị</label>
            <input type="text" name="unit" placeholder="Ví dụ: kWh, m3, month">
        </div>

        <div>
            <label>Đơn giá mặc định</label>
            <input type="number" name="default_price" min="0" step="100" required placeholder="Ví dụ: 3500">
        </div>

        <div>
            <label>Trạng thái</label>
            <select name="status" required>
                <option value="active">Đang hoạt động</option>
                <option value="inactive">Ngừng hoạt động</option>
            </select>
        </div>
    </div>

    <label>Mô tả</label>
    <textarea name="description" rows="3" placeholder="Ghi chú về dịch vụ"></textarea>

    <button type="submit">Tạo dịch vụ</button>
</form>

<h2>Danh sách dịch vụ</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/services">
        Tất cả
    </a>

    <a class="filter-link <?= $statusFilter === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/services&status=active">
        Đang hoạt động
    </a>

    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/services&status=inactive">
        Ngừng hoạt động
    </a>
</div>

<?php if (empty($services)): ?>
    <div class="alert error">Không có dịch vụ nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên dịch vụ</th>
            <th>Đơn vị</th>
            <th>Đơn giá mặc định</th>
            <th>Mô tả</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Cập nhật</th>
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
                            <option value="active" <?= $service['status'] === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="inactive" <?= $service['status'] === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                        </select>
                    </td>

                    <td><?= htmlspecialchars($service['created_at'] ?? '-') ?></td>

                    <td>
                        <button type="submit" class="btn-pay">Cập nhật</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
