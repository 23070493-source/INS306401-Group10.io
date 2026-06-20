<h1>Chỉ số điện nước</h1>
<p>Quản lý nhập chỉ số điện/nước theo phòng. Sau đó hệ thống tự chia tiền và sinh hóa đơn cho sinh viên đang có hợp đồng hiệu lực trong phòng.</p>

<form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/utility-reading-store" class="form-card wide-form">
    <h2>Tạo chỉ số điện nước</h2>

    <div class="admin-form-grid">
        <div>
            <label>Phòng</label>
            <select name="room_id" required>
                <option value="">-- Chọn phòng --</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= htmlspecialchars($room['id']) ?>">
                        <?= htmlspecialchars($room['building_name']) ?>
                        -
                        <?= htmlspecialchars($room['room_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Dịch vụ</label>
            <select name="service_id" id="service_id" required>
                <option value="">-- Chọn dịch vụ --</option>
                <?php foreach ($services as $service): ?>
                    <option 
                        value="<?= htmlspecialchars($service['id']) ?>"
                        data-price="<?= htmlspecialchars($service['default_price'] ?? 0) ?>"
                    >
                        <?= htmlspecialchars($service['service_name']) ?>
                        /
                        <?= htmlspecialchars($service['unit'] ?? '-') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Học kỳ</label>
            <select name="semester_id">
                <option value="">-- Không bắt buộc --</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?= htmlspecialchars($semester['id']) ?>">
                        <?= htmlspecialchars($semester['semester_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Tháng ghi chỉ số</label>
            <input
                type="text"
                name="reading_month"
                required
                inputmode="numeric"
                pattern="\d{4}-(0[1-9]|1[0-2])"
                placeholder="Ví dụ: 2026-08"
                value="<?= htmlspecialchars($old['reading_month'] ?? date('Y-m')) ?>"
            >
            <small>Nhập theo định dạng YYYY-MM để tránh lỗi trình duyệt tự đổi tháng.</small>
        </div>

        <div>
            <label>Chỉ số cũ</label>
            <input type="number" name="previous_reading" min="0" step="0.01" required>
        </div>

        <div>
            <label>Chỉ số mới</label>
            <input type="number" name="current_reading" min="0" step="0.01" required>
        </div>

        <div>
            <label>Đơn giá</label>
            <input type="number" name="unit_price" id="unit_price" min="0" step="100" required>
        </div>

        <div class="form-option-row form-option-wide">
            <label>
                <input type="checkbox" name="auto_generate_invoice" value="1" checked>
                Tự động sinh hóa đơn cho sinh viên trong phòng sau khi tạo chỉ số
            </label>
            <small>Bỏ chọn nếu bạn chỉ muốn lưu chỉ số trước và sinh hóa đơn sau.</small>
        </div>
    </div>

    <button type="submit">Tạo chỉ số</button>
</form>

<h2>Danh sách chỉ số</h2>

<?php if (empty($readings)): ?>
    <div class="alert error">Chưa có chỉ số điện nước nào.</div>
<?php else: ?>
    <div class="table-scroll utility-readings-table-scroll">
    <table class="utility-readings-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Phòng</th>
            <th>Dịch vụ</th>
            <th>Tháng</th>
            <th>Chỉ số cũ</th>
            <th>Chỉ số mới</th>
            <th>Tiêu thụ</th>
            <th>Đơn giá</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Hóa đơn</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($readings as $reading): ?>
            <tr>
                <td><?= htmlspecialchars($reading['id']) ?></td>

                <td>
                    <?= htmlspecialchars($reading['building_name']) ?>
                    -
                    <?= htmlspecialchars($reading['room_number']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($reading['service_name']) ?>
                    <br>
                    <small><?= htmlspecialchars($reading['unit'] ?? '-') ?></small>
                </td>

                <td><?= htmlspecialchars($reading['reading_month'] ?? '-') ?></td>

                <td><?= htmlspecialchars($reading['previous_reading']) ?></td>

                <td><?= htmlspecialchars($reading['current_reading']) ?></td>

                <td><?= htmlspecialchars($reading['consumption']) ?></td>

                <td><?= number_format((float)$reading['unit_price']) ?> VND</td>

                <td><?= number_format((float)$reading['total_amount']) ?> VND</td>

                <td>
                    <span class="badge <?= htmlspecialchars($reading['status']) ?>">
                        <?= htmlspecialchars($reading['status']) ?>
                    </span>
                </td>

                <td>
                    <?php if ($reading['status'] === 'recorded'): ?>
                        <form 
                            method="POST" 
                            action="<?= BASE_URL ?>/index.php?route=manager/utility-generate-invoice"
                            onsubmit="return confirm('Sinh hóa đơn cho chỉ số này?');"
                        >
                            <input type="hidden" name="reading_id" value="<?= htmlspecialchars($reading['id']) ?>">
                            <button type="submit" class="btn-pay">Sinh hóa đơn</button>
                        </form>
                    <?php else: ?>
                        Hóa đơn #<?= htmlspecialchars($reading['invoice_id'] ?? '-') ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const unitPriceInput = document.getElementById('unit_price');

    serviceSelect.addEventListener('change', function () {
        const option = serviceSelect.options[serviceSelect.selectedIndex];
        const price = option.getAttribute('data-price');

        if (price !== null && price !== '') {
            unitPriceInput.value = price;
        }
    });
});
</script>
