USE dormitory_manager;

-- Demo-ready billing case:
-- student003 has an active contract and receives one clean unpaid invoice
-- with no pending payment. This is useful for demonstrating Submit Bank Transfer
-- from the beginning during the project defense.

INSERT INTO invoices (
    contract_id,
    room_id,
    student_id,
    month_year,
    invoice_month,
    invoice_code,
    total_amount,
    paid_amount,
    due_date,
    status,
    created_by,
    created_at
)
SELECT
    c.id,
    c.room_id,
    c.student_id,
    '2026-07',
    '2026-07',
    'INV-DEMO-003',
    1350000.00,
    0.00,
    DATE_ADD(CURDATE(), INTERVAL 14 DAY),
    'unpaid',
    2,
    NOW()
FROM contracts c
JOIN students s ON s.id = c.student_id
JOIN users u ON u.id = s.user_id
WHERE u.username = 'student003'
  AND c.status = 'active'
  AND NOT EXISTS (
      SELECT 1
      FROM invoices i
      WHERE i.invoice_code = 'INV-DEMO-003'
  );

INSERT INTO invoice_details (
    invoice_id,
    service_id,
    description,
    quantity,
    unit_price,
    amount
)
SELECT
    i.id,
    NULL,
    'Dormitory monthly room fee - demo clean case',
    1,
    1250000.00,
    1250000.00
FROM invoices i
WHERE i.invoice_code = 'INV-DEMO-003'
  AND NOT EXISTS (
      SELECT 1
      FROM invoice_details d
      WHERE d.invoice_id = i.id
        AND d.description = 'Dormitory monthly room fee - demo clean case'
  );

INSERT INTO invoice_details (
    invoice_id,
    service_id,
    description,
    quantity,
    unit_price,
    amount
)
SELECT
    i.id,
    3,
    'Internet service - demo clean case',
    1,
    100000.00,
    100000.00
FROM invoices i
WHERE i.invoice_code = 'INV-DEMO-003'
  AND NOT EXISTS (
      SELECT 1
      FROM invoice_details d
      WHERE d.invoice_id = i.id
        AND d.description = 'Internet service - demo clean case'
  );
