<h1>Quản lý sửa chữa</h1>
<p>Quản lý xem ảnh minh chứng và cập nhật trạng thái các yêu cầu sửa chữa từ sinh viên.</p>

<div class="cards">
    <div class="card">
        <h3>Tổng yêu cầu</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Chờ xử lý</h3>
        <strong><?= htmlspecialchars($summary['pending']) ?></strong>
    </div>

    <div class="card">
        <h3>Đang xử lý</h3>
        <strong><?= htmlspecialchars($summary['in_progress']) ?></strong>
    </div>

    <div class="card">
        <h3>Hoàn tất</h3>
        <strong><?= htmlspecialchars($summary['completed']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Đã hủy</h3>
        <strong><?= htmlspecialchars($summary['cancelled']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance">
        Tất cả
    </a>

    <a class="filter-link <?= $currentStatus === 'pending' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=pending">
        Chờ xử lý
    </a>

    <a class="filter-link <?= $currentStatus === 'in_progress' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=in_progress">
        Đang xử lý
    </a>

    <a class="filter-link <?= $currentStatus === 'completed' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=completed">
        Hoàn tất
    </a>

    <a class="filter-link <?= $currentStatus === 'cancelled' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=cancelled">
        Đã hủy
    </a>
</div>

<?php if (empty($requests)): ?>
    <div class="alert error">Không có yêu cầu sửa chữa nào phù hợp.</div>
<?php else: ?>

    <div class="table-scroll maintenance-table-scroll">
    <table class="maintenance-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Sinh viên</th>
            <th>Phòng</th>
            <th>Sự cố</th>
            <th>Minh chứng</th>
            <th>Mức ưu tiên</th>
            <th>Trạng thái</th>
            <th>Ngày yêu cầu</th>
            <th>Người xử lý</th>
            <th>Cập nhật</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($requests as $request): ?>
            <?php
            $imagePath = trim($request['evidence_image'] ?? '');
            $imageUrl = $imagePath !== '' ? BASE_URL . '/' . ltrim($imagePath, '/') : '';
            ?>

            <tr>
                <td><?= htmlspecialchars($request['id']) ?></td>

                <td>
                    <?= htmlspecialchars($request['student_code'] ?? '-') ?>
                    <br>
                    <small><?= htmlspecialchars($request['full_name'] ?? '-') ?></small>
                </td>

                <td>
                    <?= htmlspecialchars($request['building_name'] ?? '-') ?>
                    -
                    <?= htmlspecialchars($request['room_number'] ?? '-') ?>
                </td>

                <td>
                    <strong><?= htmlspecialchars($request['title'] ?? '-') ?></strong>
                    <br>
                    <small>Danh mục: <?= htmlspecialchars($request['category'] ?? '-') ?></small>
                    <br>
                    <small><?= htmlspecialchars($request['description'] ?? '-') ?></small>

                    <?php if (!empty($request['resolution_note'])): ?>
                        <br>
                        <small><strong>Ghi chú:</strong> <?= htmlspecialchars($request['resolution_note']) ?></small>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($imageUrl !== ''): ?>
                        <a href="<?= htmlspecialchars($imageUrl) ?>" target="_blank">
                            <img
                                src="<?= htmlspecialchars($imageUrl) ?>"
                                alt="Ảnh minh chứng"
                                class="evidence-thumb"
                            >
                        </a>
                        <br>
                        <small>
                            <a href="<?= htmlspecialchars($imageUrl) ?>" target="_blank">
                                Xem ảnh
                            </a>
                        </small>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </td>

                <td>
                    <span class="badge priority-<?= htmlspecialchars($request['priority'] ?? 'medium') ?>">
                        <?= htmlspecialchars($request['priority'] ?? 'medium') ?>
                    </span>
                </td>

                <td>
                    <span class="badge <?= htmlspecialchars($request['status']) ?>">
                        <?= htmlspecialchars($request['status']) ?>
                    </span>
                </td>

                <td><?= htmlspecialchars($request['request_date'] ?? '-') ?></td>

                <td>
                    <?= htmlspecialchars($request['processed_by_username'] ?? '-') ?>
                    <?php if (!empty($request['processed_at'])): ?>
                        <br>
                        <small><?= htmlspecialchars($request['processed_at']) ?></small>
                    <?php endif; ?>
                </td>

                <td>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/maintenance-update" class="inline-form maintenance-update-form">
                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['id']) ?>">

                        <select name="status" required>
                            <option value="pending" <?= $request['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="in_progress" <?= $request['status'] === 'in_progress' ? 'selected' : '' ?>>Đang xử lý</option>
                            <option value="completed" <?= $request['status'] === 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
                            <option value="cancelled" <?= $request['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>

                        <input
                            type="text"
                            name="resolution_note"
                            placeholder="Ghi chú xử lý"
                            value="<?= htmlspecialchars($request['resolution_note'] ?? '') ?>"
                            class="small-input maintenance-note-input"
                        >

                        <button type="submit" class="btn-pay">
                            Cập nhật
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

<?php endif; ?>
