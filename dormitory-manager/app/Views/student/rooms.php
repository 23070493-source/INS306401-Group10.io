<?php
$totalRooms = count($rooms ?? []);
$totalAvailableBeds = 0;

foreach ($rooms ?? [] as $room) {
    $totalAvailableBeds += (int) ($room['available_beds'] ?? 0);
}
?>

<h1>Available Rooms</h1>

<?php if (empty($rooms)): ?>
    <section class="student-empty-room-page">
        <div class="empty-state">
            No available rooms at the moment.
        </div>

        <div class="page-actions">
            <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/dashboard">
                Back to Dashboard
            </a>
        </div>
    </section>
<?php else: ?>

    <section class="student-rooms-hero">
        <div>
            <span class="student-page-label">Room Availability</span>
            <h2>Find a suitable dormitory room</h2>
        </div>

        <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
            Register Room
        </a>
    </section>

    <section class="student-room-summary-grid">
        <div class="student-room-summary-card">
            <span>Available Rooms</span>
            <strong><?= htmlspecialchars((string) $totalRooms) ?></strong>
        </div>

        <div class="student-room-summary-card">
            <span>Available Beds</span>
            <strong><?= htmlspecialchars((string) $totalAvailableBeds) ?></strong>
        </div>
    </section>

    <section class="student-room-grid">
        <?php foreach ($rooms as $room): ?>
            <article class="student-room-card-item">
                <div class="student-room-card-header">
                    <div>
                        <span>Building</span>
                        <h2><?= htmlspecialchars($room['building_name'] ?? '-') ?></h2>
                    </div>

                    <span class="badge <?= htmlspecialchars($room['status'] ?? 'available') ?>">
                        <?= htmlspecialchars(ucfirst($room['status'] ?? 'available')) ?>
                    </span>
                </div>

                <div class="student-room-number">
                    Room <?= htmlspecialchars($room['room_number'] ?? '-') ?>
                </div>

                <div class="student-room-detail-grid">
                    <div>
                        <span>Type</span>
                        <strong><?= htmlspecialchars(ucfirst($room['room_type'] ?? '-')) ?></strong>
                    </div>

                    <div>
                        <span>Gender</span>
                        <strong><?= htmlspecialchars(ucfirst($room['gender_type'] ?? '-')) ?></strong>
                    </div>

                    <div>
                        <span>Capacity</span>
                        <strong><?= htmlspecialchars((string) ($room['capacity'] ?? '-')) ?></strong>
                    </div>

                    <div>
                        <span>Current</span>
                        <strong><?= htmlspecialchars((string) ($room['current_occupancy'] ?? 0)) ?></strong>
                    </div>

                    <div>
                        <span>Available Beds</span>
                        <strong><?= htmlspecialchars((string) ($room['available_beds'] ?? 0)) ?></strong>
                    </div>

                    <div>
                        <span>Price / Month</span>
                        <strong><?= number_format((float) ($room['price_per_month'] ?? 0)) ?> VND</strong>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Room List</h2>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th>Building</th>
                <th>Room</th>
                <th>Type</th>
                <th>Gender</th>
                <th>Capacity</th>
                <th>Current</th>
                <th>Available Beds</th>
                <th>Price / Month</th>
                <th>Status</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['building_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($room['room_number'] ?? '-') ?></td>
                    <td><?= htmlspecialchars(ucfirst($room['room_type'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars(ucfirst($room['gender_type'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string) ($room['capacity'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string) ($room['current_occupancy'] ?? 0)) ?></td>
                    <td>
                        <span class="badge active">
                            <?= htmlspecialchars((string) ($room['available_beds'] ?? 0)) ?>
                        </span>
                    </td>
                    <td><?= number_format((float) ($room['price_per_month'] ?? 0)) ?> VND</td>
                    <td>
                        <span class="badge <?= htmlspecialchars($room['status'] ?? 'available') ?>">
                            <?= htmlspecialchars(ucfirst($room['status'] ?? 'available')) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php endif; ?>