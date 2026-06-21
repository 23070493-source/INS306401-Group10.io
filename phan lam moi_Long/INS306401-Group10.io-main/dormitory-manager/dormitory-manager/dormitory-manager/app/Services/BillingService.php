<?php

class BillingService
{
    public function __construct(private PDO $db)
    {
    }

    public function calculateInvoiceStatus(float $totalAmount, float $paidAmount): string
    {
        if ($paidAmount <= 0) {
            return 'unpaid';
        }

        if ($paidAmount >= $totalAmount) {
            return 'paid';
        }

        return 'partially_paid';
    }

    public function createInvoice(int $contractId, string $monthYear, string $dueDate, array $details, int $createdBy): int
    {
        $stmt = $this->db->prepare("
            SELECT
                c.*,
                r.id AS room_id
            FROM contracts c
            JOIN rooms r ON r.id = c.room_id
            WHERE c.id = :contract_id
              AND c.status = 'active'
            LIMIT 1
        ");
        $stmt->execute(['contract_id' => $contractId]);
        $contract = $stmt->fetch();

        if (!$contract) {
            throw new Exception('Không tìm thấy hợp đồng active.');
        }

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM invoices
            WHERE contract_id = :contract_id
              AND month_year = :month_year
        ");
        $stmt->execute([
            'contract_id' => $contractId,
            'month_year' => $monthYear
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            throw new Exception('Hợp đồng này đã có hóa đơn trong tháng đã chọn.');
        }

        $totalAmount = 0;
        foreach ($details as $detail) {
            $totalAmount += (float) ($detail['amount'] ?? 0);
        }

        if ($totalAmount <= 0) {
            throw new Exception('Tổng tiền hóa đơn phải lớn hơn 0.');
        }

        $invoiceCode = 'INV' . date('YmdHis') . $contractId;

        $stmt = $this->db->prepare("
            INSERT INTO invoices (
                invoice_code,
                contract_id,
                student_id,
                room_id,
                month_year,
                invoice_month,
                due_date,
                total_amount,
                paid_amount,
                status,
                created_by
            )
            VALUES (
                :invoice_code,
                :contract_id,
                :student_id,
                :room_id,
                :month_year,
                :invoice_month,
                :due_date,
                :total_amount,
                0,
                'unpaid',
                :created_by
            )
        ");
        $stmt->execute([
            'invoice_code' => $invoiceCode,
            'contract_id' => $contractId,
            'student_id' => $contract['student_id'],
            'room_id' => $contract['room_id'],
            'month_year' => $monthYear,
            'invoice_month' => $monthYear,
            'due_date' => $dueDate,
            'total_amount' => $totalAmount,
            'created_by' => $createdBy
        ]);

        $invoiceId = (int) $this->db->lastInsertId();
        $detailStmt = $this->db->prepare("
            INSERT INTO invoice_details (
                invoice_id,
                service_id,
                description,
                quantity,
                unit_price,
                amount
            )
            VALUES (
                :invoice_id,
                :service_id,
                :description,
                :quantity,
                :unit_price,
                :amount
            )
        ");

        foreach ($details as $detail) {
            $detailStmt->execute([
                'invoice_id' => $invoiceId,
                'service_id' => $detail['service_id'] ?? null,
                'description' => $detail['description'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'amount' => $detail['amount']
            ]);
        }

        return $invoiceId;
    }
}
