<h1>Reports</h1>
<p>Admin xem báo cáo tổng quan về phòng, hợp đồng, hóa đơn và vi phạm.</p>

<div class="cards">
    <div class="card">
        <h3>Total Students</h3>
        <strong><?= htmlspecialchars($overview['total_students']) ?></strong>
    </div>

    <div class="card">
        <h3>Active Contracts</h3>
        <strong><?= htmlspecialchars($overview['active_contracts']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Ended Contracts</h3>
        <strong><?= htmlspecialchars($overview['ended_contracts']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Pending Registrations</h3>
        <strong><?= htmlspecialchars($overview['pending_registrations']) ?></strong>
    </div>

    <div class="card danger">
        <h3>Unpaid Invoices</h3>
        <strong><?= htmlspecialchars($overview['unpaid_invoices']) ?></strong>
    </div>

    <div class="card">
        <h3>Paid Invoices</h3>
        <strong><?= htmlspecialchars($overview['paid_invoices']) ?></strong>
    </div>

    <div class="card warning">
        <h3>Open Maintenance</h3>
        <strong><?= htmlspecialchars($overview['open_maintenance']) ?></strong>
    </div>
</div>

<h2>Occupancy By Building</h2>

<table>
    <thead>
    <tr>
        <th>Building</th>
        <th>Total Rooms</th>
        <th>Total Capacity</th>
        <th>Active Occupancy</th>
        <th>Occupancy Rate</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($occupancyByBuilding as $item): ?>
        <?php
        $capacity = (int) $item['total_capacity'];
        $occupancy = (int) $item['active_occupancy'];
        $rate = $capacity > 0 ? round($occupancy / $capacity * 100, 1) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['building_name']) ?></td>
            <td><?= htmlspecialchars($item['total_rooms']) ?></td>
            <td><?= htmlspecialchars($capacity) ?></td>
            <td><?= htmlspecialchars($occupancy) ?></td>
            <td><?= htmlspecialchars($rate) ?>%</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Invoice Summary</h2>

<table>
    <thead>
    <tr>
        <th>Status</th>
        <th>Invoice Count</th>
        <th>Total Amount</th>
        <th>Paid Amount</th>
        <th>Remaining</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($invoiceSummary as $item): ?>
        <?php
        $total = (float) $item['total_amount'];
        $paid = (float) $item['paid_amount'];
        ?>
        <tr>
            <td><span class="badge <?= htmlspecialchars($item['status']) ?>"><?= htmlspecialchars($item['status']) ?></span></td>
            <td><?= htmlspecialchars($item['invoice_count']) ?></td>
            <td><?= number_format($total) ?> VND</td>
            <td><?= number_format($paid) ?> VND</td>
            <td><?= number_format($total - $paid) ?> VND</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Top Violation Students</h2>

<?php if (empty($topViolationStudents)): ?>
    <div class="alert success">Chưa có sinh viên vi phạm.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Student Code</th>
            <th>Full Name</th>
            <th>Violation Count</th>
            <th>Total Points</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($topViolationStudents as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['student_code']) ?></td>
                <td><?= htmlspecialchars($student['full_name']) ?></td>
                <td><?= htmlspecialchars($student['violation_count']) ?></td>
                <td><span class="badge danger"><?= htmlspecialchars($student['total_points']) ?> points</span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>