<h1>Admin Dashboard</h1>
<p>Quản lý tài khoản, tòa nhà, phòng, học kỳ và dịch vụ.</p>

<div class="cards">
    <div class="card">
        <h3>Total Users</h3>
        <strong><?= $summary['total_users'] ?></strong>
    </div>

    <div class="card">
        <h3>Total Students</h3>
        <strong><?= $summary['total_students'] ?></strong>
    </div>

    <div class="card">
        <h3>Buildings</h3>
        <strong><?= $summary['total_buildings'] ?></strong>
    </div>

    <div class="card">
        <h3>Rooms</h3>
        <strong><?= $summary['total_rooms'] ?></strong>
    </div>

    <div class="card">
        <h3>Available Rooms</h3>
        <strong><?= $summary['available_rooms'] ?></strong>
    </div>

    <div class="card warning">
        <h3>Maintenance Rooms</h3>
        <strong><?= $summary['maintenance_rooms'] ?></strong>
    </div>
</div>