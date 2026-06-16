<h1>Student Dashboard</h1>
<p>Thông tin đăng ký, hợp đồng, hóa đơn và yêu cầu sửa chữa của sinh viên.</p>

<?php if (!$student): ?>
    <div class="alert error">Không tìm thấy hồ sơ sinh viên.</div>
<?php else: ?>

    <div class="profile-box">
        <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($student['student_code']) ?></p>
        <p><strong>Giới tính:</strong> <?= htmlspecialchars($student['gender']) ?></p>
        <p><strong>Khoa:</strong> <?= htmlspecialchars($student['faculty']) ?></p>
        <p><strong>Diện ưu tiên:</strong> <?= htmlspecialchars($student['priority_type']) ?></p>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Registration Status</h3>
            <strong><?= $registration ? htmlspecialchars($registration['status']) : 'No registration' ?></strong>
        </div>

        <div class="card">
            <h3>Active Contract</h3>
            <strong><?= $contract ? htmlspecialchars($contract['contract_code']) : 'No active contract' ?></strong>
        </div>

        <div class="card warning">
            <h3>Unpaid Invoices</h3>
            <strong><?= count($unpaidInvoices) ?></strong>
        </div>

        <div class="card danger">
            <h3>Violation Points</h3>
            <strong><?= htmlspecialchars($violationPoints) ?></strong>
        </div>
    </div>

    <?php if ($contract): ?>
        <h2>Current Room</h2>
        <table>
            <tr>
                <th>Building</th>
                <th>Room</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Monthly Price</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($contract['building_name']) ?></td>
                <td><?= htmlspecialchars($contract['room_number']) ?></td>
                <td><?= htmlspecialchars($contract['start_date']) ?></td>
                <td><?= htmlspecialchars($contract['end_date']) ?></td>
                <td><?= number_format($contract['monthly_price']) ?> VND</td>
            </tr>
        </table>
    <?php endif; ?>

    <h2>Recent Maintenance Requests</h2>

    <?php if (empty($maintenanceRequests)): ?>
        <p>Chưa có yêu cầu sửa chữa.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Request Date</th>
            </tr>
            <?php foreach ($maintenanceRequests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request['issue_title']) ?></td>
                    <td><?= htmlspecialchars($request['status']) ?></td>
                    <td><?= htmlspecialchars($request['request_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

<?php endif; ?>