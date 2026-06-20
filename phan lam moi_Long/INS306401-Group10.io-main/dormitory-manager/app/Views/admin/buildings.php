<h1>Tòa nhà</h1>
<p>Admin quản lý thông tin các tòa nhà trong ký túc xá.</p>

<div class="cards">
    <div class="card">
        <h3>Tổng số tòa</h3>
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
    action="<?= BASE_URL ?>/index.php?route=admin/building-store" 
    class="form-card wide-form"
>
    <h2>Tạo tòa nhà</h2>

    <div class="admin-form-grid">
        <div>
            <label>Tên tòa nhà</label>
            <input
                type="text"
                name="building_name"
                required
                placeholder="Ví dụ: Tòa A"
            >
        </div>

        <div>
            <label>Địa chỉ</label>
            <input
                type="text"
                name="address"
                placeholder="Ví dụ: Khu A - HUST Dormitory"
            >
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
    <textarea name="description" rows="3" placeholder="Ghi chú về tòa nhà"></textarea>

    <button type="submit">Tạo tòa nhà</button>
</form>

<h2>Danh sách tòa nhà</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings">
        Tất cả
    </a>

    <a class="filter-link <?= $statusFilter === 'active' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings&status=active">
        Đang hoạt động
    </a>

    <a class="filter-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/buildings&status=inactive">
        Ngừng hoạt động
    </a>
</div>

<?php if (empty($buildings)): ?>
    <div class="alert error">Không có tòa nhà nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tòa nhà</th>
            <th>Địa chỉ</th>
            <th>Số phòng</th>
            <th>Trạng thái</th>
            <th>Mô tả</th>
            <th>Ngày tạo</th>
            <th>Cập nhật nhanh</th>
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
                            <option value="active" <?= $building['status'] === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="inactive" <?= $building['status'] === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
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
                        <button type="submit" class="btn-pay">Cập nhật</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
