<h1>Create Invoice</h1>
<p>Tạo hóa đơn mới cho hợp đồng active.</p>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/index.php?route=manager/invoice-store" class="form-card wide-form">
    <label>Active Contract</label>
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
                Room <?= htmlspecialchars($contract['room_number']) ?>
                |
                <?= number_format($contract['monthly_price']) ?> VND/month
            </option>
        <?php endforeach; ?>
    </select>

    <div class="form-grid">
        <div>
            <label>Month Year</label>
            <input
                type="month"
                name="month_year"
                required
                value="<?= htmlspecialchars($old['month_year'] ?? date('Y-m')) ?>"
            >
        </div>

        <div>
            <label>Due Date</label>
            <input
                type="date"
                name="due_date"
                required
                value="<?= htmlspecialchars($old['due_date'] ?? date('Y-m-d', strtotime('+10 days'))) ?>"
            >
        </div>
    </div>

    <h2>Invoice Details</h2>

    <div class="detail-row">
        <div>
            <label>
                <input
                    type="checkbox"
                    name="include_room_rent"
                    checked
                    class="inline-checkbox"
                >
                Include Room Rent
            </label>
        </div>

        <div>
            <label>Room Rent Amount</label>
            <input
                type="number"
                name="room_rent_amount"
                min="0"
                step="1000"
                value="<?= htmlspecialchars($old['room_rent_amount'] ?? '') ?>"
                placeholder="Để trống sẽ lấy monthly_price của contract"
            >
        </div>
    </div>

    <h3>Additional Services</h3>

    <?php for ($i = 0; $i < 5; $i++): ?>
        <div class="invoice-service-row">
            <div>
                <label>Service</label>
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
                <label>Description</label>
                <input
                    type="text"
                    name="service_description[]"
                    placeholder="Ví dụ: Electricity usage"
                >
            </div>

            <div>
                <label>Quantity</label>
                <input
                    type="number"
                    name="service_quantity[]"
                    min="0"
                    step="0.01"
                    placeholder="0"
                >
            </div>

            <div>
                <label>Unit Price</label>
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

    <button type="submit">Create Invoice</button>
</form>

<div class="page-actions">
    <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=manager/invoices">
        Back to Invoices
    </a>
</div>