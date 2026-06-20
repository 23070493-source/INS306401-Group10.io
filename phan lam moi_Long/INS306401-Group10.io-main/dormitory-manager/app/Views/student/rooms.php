<?php
$totalRooms = count($rooms ?? []);
$totalAvailableBeds = 0;

$roomTypeLabels = [
    'standard' => 'Tiêu chuẩn',
    'premium' => 'Cao cấp',
];
$genderLabels = [
    'male' => 'Nam',
    'female' => 'Nữ',
    'mixed' => 'Linh hoạt',
];
$statusLabels = [
    'available' => 'Còn trống',
    'full' => 'Đã đầy',
    'maintenance' => 'Bảo trì',
];
$label = static function (array $labels, ?string $value): string {
    $key = strtolower((string) $value);
    return $labels[$key] ?? ($value ?: '-');
};

foreach ($rooms ?? [] as $room) {
    $totalAvailableBeds += (int) ($room['available_beds'] ?? 0);
}
?>

<h1>Phòng còn trống</h1>

<?php if (empty($rooms)): ?>
    <section class="student-empty-room-page">
        <div class="empty-state">
            Hiện chưa có phòng trống.
        </div>

        <div class="page-actions">
            <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/dashboard">
                Quay lại bảng điều khiển
            </a>
        </div>
    </section>
<?php else: ?>

    <section class="student-rooms-hero">
        <div>
            <span class="student-page-label">Tình trạng phòng</span>
            <h2>Tìm phòng ký túc xá phù hợp</h2>
        </div>

        <a class="student-primary-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
            Đăng ký phòng
        </a>
    </section>

    <section class="student-room-summary-grid">
        <div class="student-room-summary-card">
            <span>Số phòng còn trống</span>
            <strong><?= htmlspecialchars((string) $totalRooms) ?></strong>
        </div>

        <div class="student-room-summary-card">
            <span>Số giường trống</span>
            <strong><?= htmlspecialchars((string) $totalAvailableBeds) ?></strong>
        </div>
    </section>

    <section class="student-room-grid">
        <?php foreach ($rooms as $room): ?>
            <article class="student-room-card-item">
                <div class="student-room-card-header">
                    <div>
                        <span>Tòa nhà</span>
                        <h2><?= htmlspecialchars($room['building_name'] ?? '-') ?></h2>
                    </div>

                    <span class="badge <?= htmlspecialchars($room['status'] ?? 'available') ?>">
                        <?= htmlspecialchars($label($statusLabels, $room['status'] ?? 'available')) ?>
                    </span>
                </div>

                <div class="student-room-number">
                    Phòng <?= htmlspecialchars($room['room_number'] ?? '-') ?>
                </div>

                <div class="student-room-detail-grid">
                    <div>
                        <span>Loại phòng</span>
                        <strong><?= htmlspecialchars($label($roomTypeLabels, $room['room_type'] ?? null)) ?></strong>
                    </div>

                    <div>
                        <span>Giới tính</span>
                        <strong><?= htmlspecialchars($label($genderLabels, $room['gender_type'] ?? null)) ?></strong>
                    </div>

                    <div>
                        <span>Sức chứa</span>
                        <strong><?= htmlspecialchars((string) ($room['capacity'] ?? '-')) ?></strong>
                    </div>

                    <div>
                        <span>Đang ở</span>
                        <strong><?= htmlspecialchars((string) ($room['current_occupancy'] ?? 0)) ?></strong>
                    </div>

                    <div>
                        <span>Giường trống</span>
                        <strong><?= htmlspecialchars((string) ($room['available_beds'] ?? 0)) ?></strong>
                    </div>

                    <div>
                        <span>Giá / tháng</span>
                        <strong><?= number_format((float) ($room['price_per_month'] ?? 0)) ?> VND</strong>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="student-dashboard-section">
        <div class="student-section-header">
            <div>
                <h2>Danh sách phòng</h2>
            </div>
        </div>

        <div class="student-table-scroll">
            <table>
                <thead>
                <tr>
                    <th>Tòa nhà</th>
                    <th>Phòng</th>
                    <th>Loại phòng</th>
                    <th>Giới tính</th>
                    <th>Sức chứa</th>
                    <th>Đang ở</th>
                    <th>Giường trống</th>
                    <th>Giá / tháng</th>
                    <th>Trạng thái</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?= htmlspecialchars($room['building_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($room['room_number'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($label($roomTypeLabels, $room['room_type'] ?? null)) ?></td>
                        <td><?= htmlspecialchars($label($genderLabels, $room['gender_type'] ?? null)) ?></td>
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
                                <?= htmlspecialchars($label($statusLabels, $room['status'] ?? 'available')) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php endif; ?>
