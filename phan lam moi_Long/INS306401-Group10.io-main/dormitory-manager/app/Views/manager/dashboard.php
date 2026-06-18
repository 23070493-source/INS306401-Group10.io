<h1>Bảng điều khiển quản lý</h1>
<p>Quản lý đăng ký phòng, hợp đồng, hóa đơn, sự cố và vi phạm.</p>

<div class="cards">
    <div class="card">
        <h3>Pending Registrations</h3>
        <strong><?= $summary['pending_registrations'] ?></strong>
    </div>

    <div class="card">
        <h3>Active Contracts</h3>
        <strong><?= $summary['active_contracts'] ?></strong>
    </div>

    <div class="card warning">
        <h3>Unpaid / Overdue Invoices</h3>
        <strong><?= $summary['unpaid_invoices'] ?></strong>
    </div>

    <div class="card">
        <h3>Open Maintenance</h3>
        <strong><?= $summary['open_maintenance'] ?></strong>
    </div>

    <div class="card danger">
        <h3>Violation Warnings</h3>
        <strong><?= $summary['warning_students'] ?></strong>
    </div>
</div>
