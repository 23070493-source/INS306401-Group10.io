<h1>Semesters</h1>
<p>Admin quản lý học kỳ đăng ký ký túc xá.</p>

<div class="cards">
    <div class="card">
        <h3>Total</h3>
        <strong><?= htmlspecialchars($summary['total']) ?></strong>
    </div>

    <div class="card">
        <h3>Upcoming</h3>
        <strong><?= htmlspecialchars($summary['upcoming']) ?></strong>
    </div>

    <div class="card">
        <h3>Open</h3>
        <strong><?= htmlspecialchars($summary['open']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Closed</h3>
        <strong><?= htmlspecialchars($summary['closed']) ?></strong>
    </div>
</div>

<form 
    method="POST" 
    action="<?= BASE_URL ?>/index.php?route=admin/semester-store" 
    class="form-card wide-form"
>
    <h2>Create Semester</h2>

    <div class="admin-form-grid">
        <div>
            <label>Semester Name</label>
            <input type="text" name="semester_name" required placeholder="Ví dụ: Semester 2026A">
        </div>

        <div>
            <label>Academic Year</label>
            <input type="text" name="academic_year" required placeholder="Ví dụ: 2025-2026">
        </div>

        <div>
            <label>Start Date</label>
            <input type="date" name="start_date" required>
        </div>

        <div>
            <label>End Date</label>
            <input type="date" name="end_date" required>
        </div>

        <div>
            <label>Status</label>
            <select name="status" required>
                <option value="upcoming">upcoming</option>
                <option value="open">open</option>
                <option value="closed">closed</option>
            </select>
        </div>
    </div>

    <button type="submit">Create Semester</button>
</form>

<h2>Semester List</h2>

<div class="filter-bar">
    <a class="filter-link <?= $statusFilter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters">
        All
    </a>

    <a class="filter-link <?= $statusFilter === 'upcoming' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters&status=upcoming">
        Upcoming
    </a>

    <a class="filter-link <?= $statusFilter === 'open' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters&status=open">
        Open
    </a>

    <a class="filter-link <?= $statusFilter === 'closed' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?route=admin/semesters&status=closed">
        Closed
    </a>
</div>

<?php if (empty($semesters)): ?>
    <div class="alert error">Không có học kỳ nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Semester Name</th>
            <th>Academic Year</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Update</th>
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
                        <button type="submit" class="btn-pay">Update</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>