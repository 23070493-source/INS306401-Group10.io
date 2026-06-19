<h1>Nhật ký hệ thống</h1>
<p>Admin theo dõi lịch sử thao tác quan trọng trong hệ thống.</p>

<div class="filter-bar">
    <a class="filter-link <?= $actionFilter === '' && $tableFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/audit-logs">
        All
    </a>

    <?php foreach ($actions as $action): ?>
        <a class="filter-link <?= $actionFilter === $action['action'] ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/audit-logs&action=<?= urlencode($action['action']) ?>">
            <?= htmlspecialchars($action['action']) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="filter-bar">
    <?php foreach ($tables as $table): ?>
        <a class="filter-link <?= $tableFilter === $table['table_name'] ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/audit-logs&table=<?= urlencode($table['table_name']) ?>">
            <?= htmlspecialchars($table['table_name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($logs)): ?>
    <div class="alert error">Chưa có audit log nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Action</th>
            <th>Table</th>
            <th>Record</th>
            <th>Old Value</th>
            <th>New Value</th>
            <th>IP</th>
            <th>Created At</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['id']) ?></td>
                <td><?= htmlspecialchars($log['username'] ?? '-') ?></td>
                <td><span class="badge active"><?= htmlspecialchars($log['action']) ?></span></td>
                <td><?= htmlspecialchars($log['table_name']) ?></td>
                <td><?= htmlspecialchars($log['record_id']) ?></td>
                <td><?= htmlspecialchars($log['old_value'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['new_value'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['created_at'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
