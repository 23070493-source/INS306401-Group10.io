<h1>Utility Readings</h1>
<p>Manager nhập chỉ số điện/nước theo phòng. Sau đó hệ thống tự chia tiền và sinh invoice cho sinh viên đang có hợp đồng active trong phòng.</p>

<form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/utility-reading-store" class="form-card wide-form">
    <h2>Create Utility Reading</h2>

    <div class="admin-form-grid">
        <div>
            <label>Room</label>
            <select name="room_id" required>
                <option value="">-- Select room --</option>
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
            <label>Service</label>
            <select name="service_id" id="service_id" required>
                <option value="">-- Select service --</option>
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
            <label>Semester</label>
            <select name="semester_id">
                <option value="">-- Optional --</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?= htmlspecialchars($semester['id']) ?>">
                        <?= htmlspecialchars($semester['semester_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Reading Month</label>
            <input type="month" name="reading_month" required>
        </div>

        <div>
            <label>Previous Reading</label>
            <input type="number" name="previous_reading" min="0" step="0.01" required>
        </div>

        <div>
            <label>Current Reading</label>
            <input type="number" name="current_reading" min="0" step="0.01" required>
        </div>

        <div>
            <label>Unit Price</label>
            <input type="number" name="unit_price" id="unit_price" min="0" step="100" required>
        </div>
    </div>

    <button type="submit">Create Reading</button>
</form>

<h2>Reading List</h2>

<?php if (empty($readings)): ?>
    <div class="alert error">Chưa có utility reading nào.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Room</th>
            <th>Service</th>
            <th>Month</th>
            <th>Previous</th>
            <th>Current</th>
            <th>Consumption</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Status</th>
            <th>Invoice</th>
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
                            onsubmit="return confirm('Sinh hóa đơn cho reading này?');"
                        >
                            <input type="hidden" name="reading_id" value="<?= htmlspecialchars($reading['id']) ?>">
                            <button type="submit" class="btn-pay">Generate Invoice</button>
                        </form>
                    <?php else: ?>
                        Invoice #<?= htmlspecialchars($reading['invoice_id'] ?? '-') ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
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