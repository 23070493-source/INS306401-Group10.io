<h1>Học kỳ</h1>
<p>Admin quản lý học kỳ đăng ký ký túc xá.</p>

<div class="cards">
    <div class="card">
        <h3>Tổng số</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Sắp mở</h3>
        <strong><?= htmlspecialchars($summary['upcoming']) ?></strong>
    </div>

    <div class="card">
        <h3>Đang mở</h3>
        <strong><?= htmlspecialchars($summary['open']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Đã đóng</h3>
        <strong><?= htmlspecialchars($summary['closed']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=admin/semester-store" 
    class="form-card wide-form"
>
    <h2>Tạo học kỳ</h2>

    <div class="admin-form-grid">
        <div>
            <label>Tên học kỳ</label>
            <input type="text" name="semester_name" required placeholder="Ví dụ: Semester 2026A">
        </div>

        <div>
            <label>Năm học</label>
            <input type="text" name="academic_year" required placeholder="Ví dụ: 2025-2026">
        </div>

        <div>
            <label>Ngày bắt đầu</label>
            <input type="date" name="start_date" required>
        </div>

        <div>
            <label>Ngày kết thúc</label>
            <input type="date" name="end_date" required>
        </div>

        <div>
            <label>Trạng thái</label>
            <select name="status" required>
                <option value="upcoming">Sắp mở</option>
                <option value="open">Đang mở</option>
                <option value="closed">Đã đóng</option>
            </select>
        </div>
    </div>

    <button type="submit">Tạo học kỳ</button>
</form>

<h2>Danh sách học kỳ</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters">
        Tất cả
    </a>

    <a class="filter-link <?= $statusFilter === 'upcoming' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters&status=upcoming">
        Sắp mở
    </a>

    <a class="filter-link <?= $statusFilter === 'open' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters&status=open">
        Đang mở
    </a>

    <a class="filter-link <?= $statusFilter === 'closed' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters&status=closed">
        Đã đóng
    </a>
</div>

<?php if (empty($semesters)): ?>
    <div class="alert error">Không có học kỳ nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên học kỳ</th>
            <th>Năm học</th>
            <th>Bắt đầu</th>
            <th>Kết thúc</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Cập nhật</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($semesters as $semester): ?>
            <tr>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=admin/semester-update">
                    <td>
                        <?= htmlspecialchars($semester['id']) ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($semester['id']) ?>">
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="semester_name" 
                            value="<?= htmlspecialchars($semester['semester_name']) ?>" 
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <input 
                            type="text" 
                            name="academic_year" 
                            value="<?= htmlspecialchars($semester['academic_year']) ?>" 
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <input 
                            type="date" 
                            name="start_date" 
                            value="<?= htmlspecialchars($semester['start_date']) ?>" 
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <input 
                            type="date" 
                            name="end_date" 
                            value="<?= htmlspecialchars($semester['end_date']) ?>" 
                            required
                            class="table-input"
                        >
                    </td>

                    <td>
                        <select name="status" class="table-input">
                            <?php foreach (['upcoming', 'open', 'closed'] as $status): ?>
                                <option value="<?= $status ?>" <?= $semester['status'] === $status ? 'selected' : '' ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td><?= htmlspecialchars($semester['created_at'] ?? '-') ?></td>

                    <td>
                        <button type="submit" class="btn-pay">Cập nhật</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
