<h1>Quản lý sửa chữa</h1>
<p>Manager xem ảnh minh chứng và cập nhật trạng thái các yêu cầu sửa chữa từ sinh viên.</p>

<div class="cards">
    <div class="card">
        <h3>Total</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Pending</h3>
        <strong><?= htmlspecialchars($summary['pending']) ?></strong>
    </div>

    <div class="card">
        <h3>In Progress</h3>
        <strong><?= htmlspecialchars($summary['in_progress']) ?></strong>
    </div>

    <div class="card">
        <h3>Completed</h3>
        <strong><?= htmlspecialchars($summary['completed']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Cancelled</h3>
        <strong><?= htmlspecialchars($summary['cancelled']) ?></strong>
    </div>
</div>

<div class="filter-bar">
    <a class="filter-link <?= $currentStatus === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance">
        All
    </a>

    <a class="filter-link <?= $currentStatus === 'pending' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=pending">
        Pending
    </a>

    <a class="filter-link <?= $currentStatus === 'in_progress' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=in_progress">
        In Progress
    </a>

    <a class="filter-link <?= $currentStatus === 'completed' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=completed">
        Completed
    </a>

    <a class="filter-link <?= $currentStatus === 'cancelled' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=manager/maintenance&status=cancelled">
        Cancelled
    </a>
</div>

<?php if (empty($requests)): ?>
    <div class="alert error">Không có yêu cầu sửa chữa nào phù hợp.</div>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Room</th>
            <th>Issue</th>
            <th>Evidence</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Request Date</th>
            <th>Processed By</th>
            <th>Update</th>
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
                    <small>Category: <?= htmlspecialchars($request['category'] ?? '-') ?></small>
                    <br>
                    <small><?= htmlspecialchars($request['description'] ?? '-') ?></small>

                    <?php if (!empty($request['resolution_note'])): ?>
                        <br>
                        <small><strong>Note:</strong> <?= htmlspecialchars($request['resolution_note']) ?></small>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($imageUrl !== ''): ?>
                        <a href="<?= htmlspecialchars($imageUrl) ?>" target="_blank">
                            <img
                                src="<?= htmlspecialchars($imageUrl) ?>"
                                alt="Evidence Image"
                                class="evidence-thumb"
                            >
                        </a>
                        <br>
                        <small>
                            <a href="<?= htmlspecialchars($imageUrl) ?>" target="_blank">
                                View image
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
                            <option value="pending" <?= $request['status'] === 'pending' ? 'selected' : '' ?>>pending</option>
                            <option value="in_progress" <?= $request['status'] === 'in_progress' ? 'selected' : '' ?>>in_progress</option>
                            <option value="completed" <?= $request['status'] === 'completed' ? 'selected' : '' ?>>completed</option>
                            <option value="cancelled" <?= $request['status'] === 'cancelled' ? 'selected' : '' ?>>cancelled</option>
                        </select>

                        <input
                            type="text"
                            name="resolution_note"
                            placeholder="Resolution note"
                            value="<?= htmlspecialchars($request['resolution_note'] ?? '') ?>"
                            class="small-input maintenance-note-input"
                        >

                        <button type="submit" class="btn-pay">
                            Update
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
