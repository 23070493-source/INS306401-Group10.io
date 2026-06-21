<h1>Tạo hóa đơn</h1>
<p>Tạo hóa đơn mới cho hợp đồng đang hiệu lực.</p>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/invoice-store" class="form-card wide-form">
    <label>Hợp đồng đang hiệu lực</label>
    <select name="contract_id" required>
        <option value="">-- Chọn hợp đồng --</option>

        <?php foreach ($contracts as $contract): ?>
            <option
                value="<?= htmlspecialchars($contract['id']) ?>"
                data-price="<?= htmlspecialchars($contract['monthly_price']) ?>"
                <?= (($old['contract_id'] ?? '') == $contract['id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($contract['contract_code']) ?>
                |
                <?= htmlspecialchars($contract['student_code']) ?>
                -
                <?= htmlspecialchars($contract['full_name']) ?>
                |
                <?= htmlspecialchars($contract['building_name']) ?>
                -
                Phòng <?= htmlspecialchars($contract['room_number']) ?>
                |
                <?= number_format($contract['monthly_price']) ?> VND/tháng
            </option>
        <?php endforeach; ?>
    </select>

    <div class="form-grid">
        <div>
            <label>Tháng lập hóa đơn</label>
            <input
                type="month"
                name="month_year"
                required
                value="<?= htmlspecialchars($old['month_year'] ?? date('Y-m')) ?>"
            >
        </div>

        <div>
            <label>Hạn thanh toán</label>
            <input
                type="date"
                name="due_date"
                required
                value="<?= htmlspecialchars($old['due_date'] ?? date('Y-m-d', strtotime('+10 days'))) ?>"
            >
        </div>
    </div>

    <h2>Chi tiết hóa đơn</h2>

    <div class="detail-row">
        <div>
            <label>
                <input
                    type="checkbox"
                    name="include_room_rent"
                    checked
                    class="inline-checkbox"
                >
                Tính tiền phòng
            </label>
        </div>

        <div>
            <label>Số tiền phòng</label>
            <input
                type="number"
                name="room_rent_amount"
                min="0"
                step="1000"
                value="<?= htmlspecialchars($old['room_rent_amount'] ?? '') ?>"
                placeholder="Để trống sẽ lấy giá phòng hàng tháng của hợp đồng"
            >
        </div>
    </div>

    <h3>Dịch vụ phát sinh</h3>

    <?php for ($i = 0; $i < 5; $i++): ?>
        <div class="invoice-service-row">
            <div>
                <label>Dịch vụ</label>
                <select name="service_id[]">
                    <option value="">-- Không chọn --</option>

                    <?php foreach ($services as $service): ?>
                        <option value="<?= htmlspecialchars($service['id']) ?>">
                            <?= htmlspecialchars($service['service_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Mô tả</label>
                <input
                    type="text"
                    name="service_description[]"
                    placeholder="Ví dụ: Tiền điện phát sinh"
                >
            </div>

            <div>
                <label>Số lượng</label>
                <input
                    type="number"
                    name="service_quantity[]"
                    min="0"
                    step="0.01"
                    placeholder="0"
                >
            </div>

            <div>
                <label>Đơn giá</label>
                <input
                    type="number"
                    name="service_unit_price[]"
                    min="0"
                    step="100"
                    placeholder="0"
                >
            </div>
        </div>
    <?php endfor; ?>

    <button type="submit">Tạo hóa đơn</button>
</form>

<div class="page-actions">
    <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=manager/invoices">
        Quay lại danh sách hóa đơn
    </a>
</div>
