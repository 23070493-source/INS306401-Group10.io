<?php

if (!function_exists('managerColumnExists')) {
    function managerColumnExists(PDO $db, string $tableName, string $columnName): bool
    {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name
        ");

        $stmt->execute([
            'table_name' => $tableName,
            'column_name' => $columnName
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

if (!function_exists('managerAddColumnIfMissing')) {
    function managerAddColumnIfMissing(PDO $db, string $tableName, string $columnName, string $columnDefinition): void
    {
        if (!managerColumnExists($db, $tableName, $columnName)) {
            $db->exec("ALTER TABLE {$tableName} ADD COLUMN {$columnDefinition}");
        }
    }
}

if (!function_exists('managerEnsureContractTerminationSchema')) {
    function managerEnsureContractTerminationSchema(PDO $db): void
    {
        managerAddColumnIfMissing($db, 'contracts', 'ended_at', 'ended_at DATETIME NULL');
        managerAddColumnIfMissing($db, 'contracts', 'ended_by', 'ended_by INT NULL');
        managerAddColumnIfMissing($db, 'contracts', 'checkout_note', 'checkout_note TEXT NULL');
    }
}

if (!function_exists('managerEnsureUtilitySchema')) {
    function managerEnsureUtilitySchema(PDO $db): void
    {
        managerAddColumnIfMissing($db, 'utility_readings', 'service_id', 'service_id INT NULL');
        managerAddColumnIfMissing($db, 'utility_readings', 'semester_id', 'semester_id INT NULL');
        managerAddColumnIfMissing($db, 'utility_readings', 'reading_month', 'reading_month VARCHAR(7) NULL');
        managerAddColumnIfMissing($db, 'utility_readings', 'previous_reading', 'previous_reading DECIMAL(12,2) NOT NULL DEFAULT 0');
        managerAddColumnIfMissing($db, 'utility_readings', 'current_reading', 'current_reading DECIMAL(12,2) NOT NULL DEFAULT 0');
        managerAddColumnIfMissing($db, 'utility_readings', 'consumption', 'consumption DECIMAL(12,2) NOT NULL DEFAULT 0');
        managerAddColumnIfMissing($db, 'utility_readings', 'unit_price', 'unit_price DECIMAL(12,2) NOT NULL DEFAULT 0');
        managerAddColumnIfMissing($db, 'utility_readings', 'total_amount', 'total_amount DECIMAL(12,2) NOT NULL DEFAULT 0');
        managerAddColumnIfMissing($db, 'utility_readings', 'invoice_id', 'invoice_id INT NULL');
        managerAddColumnIfMissing($db, 'utility_readings', 'recorded_by', 'recorded_by INT NULL');
        managerAddColumnIfMissing($db, 'utility_readings', 'recorded_at', 'recorded_at DATETIME NULL');
        managerAddColumnIfMissing($db, 'utility_readings', 'status', "status VARCHAR(30) NOT NULL DEFAULT 'recorded'");

        managerAddColumnIfMissing($db, 'services', 'unit', 'unit VARCHAR(50) NULL');
        managerAddColumnIfMissing($db, 'services', 'default_price', 'default_price DECIMAL(12,2) NOT NULL DEFAULT 0');
        managerAddColumnIfMissing($db, 'services', 'status', "status VARCHAR(20) NOT NULL DEFAULT 'active'");

        managerAddColumnIfMissing($db, 'invoices', 'invoice_month', 'invoice_month VARCHAR(7) NULL');
        managerAddColumnIfMissing($db, 'invoices', 'due_date', 'due_date DATE NULL');
        managerAddColumnIfMissing($db, 'invoices', 'created_by', 'created_by INT NULL');
        managerAddColumnIfMissing($db, 'invoices', 'created_at', 'created_at DATETIME NULL');

        managerAddColumnIfMissing($db, 'invoice_details', 'amount', 'amount DECIMAL(12,2) NOT NULL DEFAULT 0');
    }
}


if (!function_exists('managerEnsurePaymentSchema')) {
    function managerEnsurePaymentSchema(PDO $db): void
    {
        managerAddColumnIfMissing($db, 'payments', 'payment_proof_image', 'payment_proof_image VARCHAR(255) NULL');
        managerAddColumnIfMissing($db, 'payments', 'gateway_status', "gateway_status VARCHAR(50) NULL DEFAULT 'manual_pending'");
        managerAddColumnIfMissing($db, 'payments', 'gateway_transaction_id', 'gateway_transaction_id VARCHAR(100) NULL');
        managerAddColumnIfMissing($db, 'payments', 'transfer_content', 'transfer_content VARCHAR(255) NULL');
    }
}

if (!function_exists('managerNormalizeReadingMonth')) {
    function managerNormalizeReadingMonth(string $value): ?string
    {
        $value = trim(str_replace('/', '-', $value));

        if (preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{4})-([1-9])$/', $value, $matches)) {
            return $matches[1] . '-0' . $matches[2];
        }

        return null;
    }
}

if (!function_exists('ktxAuditSafe')) {
    function ktxAuditSafe(PDO $db, int $userId, string $action, string $tableName, int $recordId, ?string $oldValue, ?string $newValue): void
    {
        $stmt = $db->prepare("
            INSERT INTO audit_logs (
                user_id,
                action,
                table_name,
                record_id,
                old_value,
                new_value,
                ip_address,
                user_agent
            )
            VALUES (
                :user_id,
                :action,
                :table_name,
                :record_id,
                :old_value,
                :new_value,
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }
}

if (!function_exists('managerUpdateInvoiceStatusByAmounts')) {
    function managerUpdateInvoiceStatusByAmounts(PDO $db, int $invoiceId): void
    {
        $stmt = $db->prepare("SELECT total_amount, paid_amount FROM invoices WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $invoiceId]);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            return;
        }

        $totalAmount = (float) ($invoice['total_amount'] ?? 0);
        $paidAmount = (float) ($invoice['paid_amount'] ?? 0);

        if ($totalAmount <= 0) {
            $status = 'unpaid';
        } elseif ($paidAmount >= $totalAmount) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partially_paid';
        } else {
            $status = 'unpaid';
        }

        $stmt = $db->prepare("UPDATE invoices SET status = :status WHERE id = :id");
        $stmt->execute([
            'status' => $status,
            'id' => $invoiceId
        ]);
    }
}

if (!function_exists('managerFindOrCreateMonthlyInvoice')) {
    function managerFindOrCreateMonthlyInvoice(PDO $db, array $contract, array $reading, int $managerId): int
    {
        $stmt = $db->prepare("
            SELECT id
            FROM invoices
            WHERE contract_id = :contract_id
              AND month_year = :month_year
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([
            'contract_id' => $contract['contract_id'],
            'month_year' => $reading['reading_month']
        ]);

        $existingInvoiceId = (int) $stmt->fetchColumn();

        if ($existingInvoiceId > 0) {
            return $existingInvoiceId;
        }

        $invoiceCode = 'INVU' . date('YmdHis') . $reading['id'] . $contract['contract_id'];

        $stmt = $db->prepare("
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
                created_by,
                created_at
            )
            VALUES (
                :invoice_code,
                :contract_id,
                :student_id,
                :room_id,
                :month_year,
                :invoice_month,
                DATE_ADD(CURDATE(), INTERVAL 7 DAY),
                0,
                0,
                'unpaid',
                :created_by,
                NOW()
            )
        ");

        $stmt->execute([
            'invoice_code' => $invoiceCode,
            'contract_id' => $contract['contract_id'],
            'student_id' => $contract['student_id'],
            'room_id' => $contract['room_id'],
            'month_year' => $reading['reading_month'],
            'invoice_month' => $reading['reading_month'],
            'created_by' => $managerId
        ]);

        return (int) $db->lastInsertId();
    }
}

if (!function_exists('managerAppendUtilityDetailToInvoice')) {
    function managerAppendUtilityDetailToInvoice(PDO $db, int $invoiceId, array $reading, float $shareConsumption, float $shareAmount): void
    {
        $description = ($reading['service_name'] ?? 'Dịch vụ')
            . ' phòng '
            . ($reading['room_number'] ?? '-')
            . ' - '
            . ($reading['reading_month'] ?? '-');

        $stmt = $db->prepare("
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

        $stmt->execute([
            'invoice_id' => $invoiceId,
            'service_id' => $reading['service_id'],
            'description' => $description,
            'quantity' => $shareConsumption,
            'unit_price' => $reading['unit_price'],
            'amount' => $shareAmount
        ]);

        $stmt = $db->prepare("
            UPDATE invoices
            SET total_amount = total_amount + :amount
            WHERE id = :invoice_id
        ");

        $stmt->execute([
            'amount' => $shareAmount,
            'invoice_id' => $invoiceId
        ]);

        managerUpdateInvoiceStatusByAmounts($db, $invoiceId);
    }
}

if (!function_exists('managerGenerateUtilityInvoicesFromReading')) {
    function managerGenerateUtilityInvoicesFromReading(PDO $db, int $readingId, int $managerId): array
    {
        $stmt = $db->prepare("
            SELECT
                ur.*,
                sv.service_name,
                sv.unit,
                r.room_number
            FROM utility_readings ur
            JOIN rooms r ON r.id = ur.room_id
            LEFT JOIN services sv ON sv.id = ur.service_id
            WHERE ur.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $readingId
        ]);

        $reading = $stmt->fetch();

        if (!$reading) {
            throw new Exception('Không tìm thấy chỉ số điện/nước.');
        }

        if (($reading['status'] ?? '') === 'invoiced') {
            throw new Exception('Chỉ số này đã được sinh hóa đơn.');
        }

        if (empty($reading['service_id'])) {
            throw new Exception('Chỉ số này chưa gắn dịch vụ nên chưa thể sinh hóa đơn.');
        }

        $stmt = $db->prepare("
            SELECT
                c.id AS contract_id,
                c.student_id,
                c.room_id
            FROM contracts c
            WHERE c.room_id = :room_id
              AND c.status = 'active'
            ORDER BY c.id
        ");

        $stmt->execute([
            'room_id' => $reading['room_id']
        ]);

        $contracts = $stmt->fetchAll();

        if (empty($contracts)) {
            throw new Exception('Phòng này chưa có hợp đồng đang hiệu lực để sinh hóa đơn.');
        }

        $studentCount = count($contracts);
        $shareAmount = round(((float) $reading['total_amount']) / $studentCount, 2);
        $shareConsumption = round(((float) $reading['consumption']) / $studentCount, 2);
        $firstInvoiceId = null;
        $invoiceIds = [];

        foreach ($contracts as $contract) {
            $invoiceId = managerFindOrCreateMonthlyInvoice($db, $contract, $reading, $managerId);
            $firstInvoiceId = $firstInvoiceId ?? $invoiceId;
            $invoiceIds[] = $invoiceId;

            managerAppendUtilityDetailToInvoice($db, $invoiceId, $reading, $shareConsumption, $shareAmount);
            ktxAuditSafe($db, $managerId, 'update', 'invoices', $invoiceId, null, 'Added utility detail from reading #' . $readingId);
        }

        $stmt = $db->prepare("
            UPDATE utility_readings
            SET
                status = 'invoiced',
                invoice_id = :invoice_id
            WHERE id = :id
        ");

        $stmt->execute([
            'invoice_id' => $firstInvoiceId,
            'id' => $readingId
        ]);

        return [
            'invoice_count' => count(array_unique($invoiceIds)),
            'first_invoice_id' => $firstInvoiceId
        ];
    }
}

if (!function_exists('ktxSyncRoomStatus')) {
    function ktxSyncRoomStatus(PDO $db, int $roomId): void
    {
        if ($roomId <= 0) {
            return;
        }

        $stmt = $db->prepare("
            SELECT id, capacity, status
            FROM rooms
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $roomId
        ]);

        $room = $stmt->fetch();

        if (!$room) {
            return;
        }

        if (in_array($room['status'], ['maintenance', 'inactive'], true)) {
            return;
        }

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM contracts
            WHERE room_id = :room_id
              AND status = 'active'
        ");

        $stmt->execute([
            'room_id' => $roomId
        ]);

        $activeCount = (int) $stmt->fetchColumn();
        $capacity = (int) $room['capacity'];
        $newStatus = $activeCount >= $capacity ? 'full' : 'available';

        $stmt = $db->prepare("
            UPDATE rooms
            SET status = :status
            WHERE id = :id
        ");

        $stmt->execute([
            'status' => $newStatus,
            'id' => $roomId
        ]);
    }
}

if (!function_exists('managerViolationPointRules')) {
    function managerViolationPointRules(): array
    {
        return [
            'Late return' => 2,
            'Noise disturbance' => 3,
            'Poor hygiene' => 3,
            'Unauthorized room change' => 5,
            'Unpaid fee' => 5,
            'Damage to property' => 7,
            'Smoking or alcohol violation' => 10,
            'Other' => null
        ];
    }
}

if (!function_exists('managerGetWarningStudents')) {
    function managerGetWarningStudents(PDO $db): array
    {
        return $db->query("
            SELECT
                s.id,
                s.student_code,
                s.full_name,
                s.faculty,
                COALESCE(v.total_points, 0) AS total_points,
                COALESCE(v.violation_count, 0) AS violation_count,
                c.id AS active_contract_id,
                c.contract_code AS active_contract_code,
                r.room_number,
                b.building_name
            FROM students s
            LEFT JOIN (
                SELECT
                    student_id,
                    SUM(penalty_points) AS total_points,
                    COUNT(*) AS violation_count
                FROM violation_records
                GROUP BY student_id
            ) v ON v.student_id = s.id
            LEFT JOIN contracts c ON c.id = (
                SELECT c2.id
                FROM contracts c2
                WHERE c2.student_id = s.id
                  AND c2.status = 'active'
                ORDER BY c2.id DESC
                LIMIT 1
            )
            LEFT JOIN rooms r ON r.id = c.room_id
            LEFT JOIN buildings b ON b.id = r.building_id
            WHERE COALESCE(v.total_points, 0) >= 5
            ORDER BY
                total_points DESC,
                violation_count DESC
        ")->fetchAll();
    }
}

if (!function_exists('managerViolationSummary')) {
    function managerViolationSummary(PDO $db, array $warningStudents, int $criticalThreshold = 15): array
    {
        $summary = [
            'total_violations' => $db->query("SELECT COUNT(*) FROM violation_records")->fetchColumn(),
            'warning_students' => count($warningStudents),
            'serious_students' => 0,
            'critical_students' => 0,
        ];

        foreach ($warningStudents as $warningStudent) {
            $points = (int) $warningStudent['total_points'];

            if ($points >= $criticalThreshold) {
                $summary['critical_students']++;
            } elseif ($points >= 10) {
                $summary['serious_students']++;
            }
        }

        return $summary;
    }
}

$routes['manager/dashboard'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $summary = [
        'pending_registrations' => $db->query("SELECT COUNT(*) FROM room_registrations WHERE status = 'pending'")->fetchColumn(),
        'active_contracts' => $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'active'")->fetchColumn(),
        'unpaid_invoices' => $db->query("SELECT COUNT(*) FROM invoices WHERE status IN ('unpaid', 'overdue', 'partially_paid')")->fetchColumn(),
        'open_maintenance' => $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status IN ('pending', 'in_progress')")->fetchColumn(),
    ];

    $warningStudents = $db->query("
        SELECT COUNT(*) 
        FROM (
            SELECT student_id
            FROM violation_records
            GROUP BY student_id
            HAVING SUM(penalty_points) >= 5
        ) AS warning_students
    ")->fetchColumn();

    $summary['warning_students'] = $warningStudents;

    render('manager/dashboard', [
        'title' => 'Bảng điều khiển quản lý',
        'summary' => $summary
    ]);
};

$routes['manager/registrations'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $stmt = $db->query("
        SELECT 
            rr.id,
            rr.status,
            rr.priority_score,
            rr.created_at,
            s.student_code,
            s.full_name,
            s.gender,
            s.priority_type,
            se.semester_name,
            se.academic_year,
            b.building_name AS desired_building,
            rr.desired_room_type,
            rr.desired_gender_type
        FROM room_registrations rr
        JOIN students s ON s.id = rr.student_id
        JOIN semesters se ON se.id = rr.semester_id
        LEFT JOIN buildings b ON b.id = rr.desired_building_id
        ORDER BY 
            CASE rr.status
                WHEN 'pending' THEN 1
                WHEN 'approved' THEN 2
                WHEN 'rejected' THEN 3
                ELSE 4
            END,
            rr.priority_score DESC,
            rr.created_at ASC
    ");

    render('manager/registrations', [
        'title' => 'Đơn đăng ký phòng',
        'registrations' => $stmt->fetchAll()
    ]);
};

$routes['manager/registration-detail'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $registrationId = (int) ($_GET['id'] ?? 0);

    if ($registrationId <= 0) {
        redirectTo('manager/registrations');
    }

    $stmt = $db->prepare("
        SELECT 
            rr.*,
            s.student_code,
            s.full_name,
            s.gender,
            s.dob,
            s.faculty,
            s.program,
            s.priority_type,
            s.address,
            se.semester_name,
            se.academic_year,
            se.start_date,
            se.end_date,
            desired_b.building_name AS desired_building,
            assigned_r.room_number AS assigned_room,
            assigned_b.building_name AS assigned_building
        FROM room_registrations rr
        JOIN students s ON s.id = rr.student_id
        JOIN semesters se ON se.id = rr.semester_id
        LEFT JOIN buildings desired_b ON desired_b.id = rr.desired_building_id
        LEFT JOIN rooms assigned_r ON assigned_r.id = rr.assigned_room_id
        LEFT JOIN buildings assigned_b ON assigned_b.id = assigned_r.building_id
        WHERE rr.id = :id
        LIMIT 1
    ");

    $stmt->execute([
        'id' => $registrationId
    ]);

    $registration = $stmt->fetch();

    if (!$registration) {
        http_response_code(404);
        echo 'Registration not found';
        return;
    }

    $params = [
        'gender' => $registration['gender'],
        'room_type' => $registration['desired_room_type']
    ];

    $buildingCondition = '';

    if (!empty($registration['desired_building_id'])) {
        $buildingCondition = 'AND r.building_id = :building_id';
        $params['building_id'] = $registration['desired_building_id'];
    }

    $stmt = $db->prepare("
        SELECT 
            r.id AS room_id,
            b.building_name,
            r.room_number,
            r.room_type,
            r.gender_type,
            r.capacity,
            COUNT(c.id) AS current_occupancy,
            r.capacity - COUNT(c.id) AS available_beds,
            r.price_per_month,
            r.status
        FROM rooms r
        JOIN buildings b ON b.id = r.building_id
        LEFT JOIN contracts c 
            ON c.room_id = r.id 
            AND c.status = 'active'
        WHERE r.status = 'available'
          AND r.room_type = :room_type
          AND r.gender_type IN (:gender, 'mixed')
          $buildingCondition
        GROUP BY 
            r.id,
            b.building_name,
            r.room_number,
            r.room_type,
            r.gender_type,
            r.capacity,
            r.price_per_month,
            r.status
        HAVING current_occupancy < r.capacity
        ORDER BY available_beds DESC, b.building_name, r.room_number
    ");

    $stmt->execute($params);

    render('manager/registration_detail', [
        'title' => 'Chi tiết đơn đăng ký',
        'registration' => $registration,
        'availableRooms' => $stmt->fetchAll()
    ]);
};

$routes['manager/registration-approve'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/registrations');
    }

    $manager = Auth::user();
    $registrationId = (int) ($_POST['registration_id'] ?? 0);
    $roomId = (int) ($_POST['room_id'] ?? 0);

    if ($registrationId <= 0 || $roomId <= 0) {
        redirectTo('manager/registrations');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT 
                rr.*,
                s.id AS student_id,
                se.start_date,
                se.end_date
            FROM room_registrations rr
            JOIN students s ON s.id = rr.student_id
            JOIN semesters se ON se.id = rr.semester_id
            WHERE rr.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $registrationId
        ]);

        $registration = $stmt->fetch();

        if (!$registration) {
            throw new Exception('Không tìm thấy đơn đăng ký.');
        }

        if ($registration['status'] !== 'pending') {
            throw new Exception('Đơn này không còn ở trạng thái pending.');
        }

        $stmt = $db->prepare("
            SELECT 
                r.id,
                r.room_number,
                r.room_type,
                r.gender_type,
                r.capacity,
                r.price_per_month,
                r.status,
                COUNT(c.id) AS current_occupancy
            FROM rooms r
            LEFT JOIN contracts c 
                ON c.room_id = r.id 
                AND c.status = 'active'
            WHERE r.id = :room_id
            GROUP BY 
                r.id,
                r.room_number,
                r.room_type,
                r.gender_type,
                r.capacity,
                r.price_per_month,
                r.status
            LIMIT 1
        ");

        $stmt->execute([
            'room_id' => $roomId
        ]);

        $room = $stmt->fetch();

        if (!$room) {
            throw new Exception('Không tìm thấy phòng.');
        }

        if ($room['status'] !== 'available') {
            throw new Exception('Phòng này hiện không khả dụng.');
        }

        if ((int) $room['current_occupancy'] >= (int) $room['capacity']) {
            throw new Exception('Phòng này đã hết chỗ.');
        }

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM contracts
            WHERE student_id = :student_id
              AND status = 'active'
        ");

        $stmt->execute([
            'student_id' => $registration['student_id']
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            throw new Exception('Sinh viên này đã có hợp đồng active.');
        }

        $stmt = $db->prepare("
            UPDATE room_registrations
            SET 
                status = 'approved',
                assigned_room_id = :room_id,
                processed_by = :processed_by,
                processed_at = NOW(),
                rejection_reason = NULL
            WHERE id = :registration_id
        ");

        $stmt->execute([
            'room_id' => $roomId,
            'processed_by' => $manager['id'],
            'registration_id' => $registrationId
        ]);

        $contractCode = 'CT' . date('YmdHis') . $registrationId;

        $stmt = $db->prepare("
            INSERT INTO contracts (
                registration_id,
                contract_code,
                student_id,
                room_id,
                semester_id,
                start_date,
                end_date,
                monthly_price,
                deposit_amount,
                status,
                created_by
            )
            VALUES (
                :registration_id,
                :contract_code,
                :student_id,
                :room_id,
                :semester_id,
                :start_date,
                :end_date,
                :monthly_price,
                :deposit_amount,
                'active',
                :created_by
            )
        ");

        $stmt->execute([
            'registration_id' => $registrationId,
            'contract_code' => $contractCode,
            'student_id' => $registration['student_id'],
            'room_id' => $roomId,
            'semester_id' => $registration['semester_id'],
            'start_date' => $registration['start_date'],
            'end_date' => $registration['end_date'],
            'monthly_price' => $room['price_per_month'],
            'deposit_amount' => $room['price_per_month'],
            'created_by' => $manager['id']
        ]);

        ktxSyncRoomStatus($db, $roomId);

        $stmt = $db->prepare("
            INSERT INTO audit_logs (
                user_id,
                action,
                table_name,
                record_id,
                old_value,
                new_value,
                ip_address,
                user_agent
            )
            VALUES (
                :user_id,
                'update',
                'room_registrations',
                :record_id,
                'pending',
                'approved',
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $manager['id'],
            'record_id' => $registrationId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);

        $db->commit();

        redirectTo('manager/registrations');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Approve failed</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/registration-detail&id=' . $registrationId . '">Go back</a>';
        exit;
    }
};

$routes['manager/registration-reject'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/registrations');
    }

    $manager = Auth::user();
    $registrationId = (int) ($_POST['registration_id'] ?? 0);
    $reason = trim($_POST['rejection_reason'] ?? '');

    if ($registrationId <= 0) {
        redirectTo('manager/registrations');
    }

    if ($reason === '') {
        $reason = 'Không đáp ứng điều kiện xếp phòng hiện tại.';
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT *
            FROM room_registrations
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $registrationId
        ]);

        $registration = $stmt->fetch();

        if (!$registration) {
            throw new Exception('Không tìm thấy đơn đăng ký.');
        }

        if ($registration['status'] !== 'pending') {
            throw new Exception('Đơn này không còn ở trạng thái pending.');
        }

        $stmt = $db->prepare("
            UPDATE room_registrations
            SET 
                status = 'rejected',
                assigned_room_id = NULL,
                processed_by = :processed_by,
                processed_at = NOW(),
                rejection_reason = :reason
            WHERE id = :registration_id
        ");

        $stmt->execute([
            'processed_by' => $manager['id'],
            'reason' => $reason,
            'registration_id' => $registrationId
        ]);

        $stmt = $db->prepare("
            INSERT INTO audit_logs (
                user_id,
                action,
                table_name,
                record_id,
                old_value,
                new_value,
                ip_address,
                user_agent
            )
            VALUES (
                :user_id,
                'update',
                'room_registrations',
                :record_id,
                'pending',
                'rejected',
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $manager['id'],
            'record_id' => $registrationId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);

        $db->commit();

        redirectTo('manager/registrations');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Reject failed</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/registration-detail&id=' . $registrationId . '">Go back</a>';
        exit;
    }
};

$routes['manager/contracts'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureContractTerminationSchema($db);

    $status = trim($_GET['status'] ?? '');
    $allowedStatuses = ['active', 'expired', 'terminated', 'cancelled'];

    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, $allowedStatuses, true)) {
        $where = 'WHERE c.status = :status';
        $params['status'] = $status;
    }

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM contracts")->fetchColumn(),
        'active' => $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'active'")->fetchColumn(),
        'expired' => $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'expired'")->fetchColumn(),
        'terminated' => $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'terminated'")->fetchColumn(),
    ];

    $stmt = $db->prepare("
        SELECT 
            c.id,
            c.contract_code,
            c.start_date,
            c.end_date,
            c.monthly_price,
            c.deposit_amount,
            c.status,
            c.created_at,
            c.ended_at,
            c.checkout_note,
            s.student_code,
            s.full_name,
            r.room_number,
            r.room_type,
            r.gender_type AS gender,
            b.building_name,
            se.semester_name,
            se.academic_year,
            creator.username AS created_by_username,
            ended_user.username AS ended_by_username
        FROM contracts c
        JOIN students s ON s.id = c.student_id
        JOIN rooms r ON r.id = c.room_id
        JOIN buildings b ON b.id = r.building_id
        JOIN semesters se ON se.id = c.semester_id
        LEFT JOIN users creator ON creator.id = c.created_by
        LEFT JOIN users ended_user ON ended_user.id = c.ended_by
        $where
        ORDER BY 
            CASE c.status
                WHEN 'active' THEN 1
                WHEN 'expired' THEN 2
                WHEN 'terminated' THEN 3
                WHEN 'cancelled' THEN 4
                ELSE 5
            END,
            c.created_at DESC
    ");

    $stmt->execute($params);

    render('manager/contracts', [
        'title' => 'Hợp đồng',
        'contracts' => $stmt->fetchAll(),
        'summary' => $summary,
        'currentStatus' => $status
    ]);
};

$routes['manager/contract-print'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureContractTerminationSchema($db);

    $contractId = (int) ($_GET['contract_id'] ?? 0);

    if ($contractId <= 0) {
        redirectTo('manager/contracts');
    }

    $stmt = $db->prepare("
        SELECT
            c.*,
            s.student_code,
            s.full_name,
            s.gender AS student_gender,
            s.faculty,
            s.program,
            r.room_number,
            r.room_type,
            r.gender_type,
            r.capacity,
            b.building_name,
            se.semester_name,
            se.academic_year,
            creator.username AS created_by_username
        FROM contracts c
        JOIN students s ON s.id = c.student_id
        JOIN rooms r ON r.id = c.room_id
        JOIN buildings b ON b.id = r.building_id
        JOIN semesters se ON se.id = c.semester_id
        LEFT JOIN users creator ON creator.id = c.created_by
        WHERE c.id = :contract_id
        LIMIT 1
    ");

    $stmt->execute(['contract_id' => $contractId]);
    $contract = $stmt->fetch();

    if (!$contract) {
        echo '<h2>Không tìm thấy hợp đồng.</h2>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/contracts">Quay lại</a>';
        return;
    }

    render('print/contract', [
        'title' => 'In hợp đồng',
        'contract' => $contract,
        'backUrl' => BASE_URL . '/index.php?route=manager/contracts'
    ]);
};

$routes['manager/contract-end'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureContractTerminationSchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/contracts');
    }

    $manager = Auth::user();
    $contractId = (int) ($_POST['contract_id'] ?? 0);
    $checkoutNote = trim($_POST['checkout_note'] ?? '');

    if ($contractId <= 0) {
        redirectTo('manager/contracts');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT *
            FROM contracts
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $contractId
        ]);

        $contract = $stmt->fetch();

        if (!$contract) {
            throw new Exception('Không tìm thấy hợp đồng.');
        }

        if ($contract['status'] !== 'active') {
            throw new Exception('Chỉ có thể checkout hợp đồng đang active.');
        }

        $stmt = $db->prepare("
            UPDATE contracts
            SET
                status = 'terminated',
                ended_at = NOW(),
                ended_by = :ended_by,
                checkout_note = :checkout_note
            WHERE id = :id
        ");

        $stmt->execute([
            'ended_by' => $manager['id'],
            'checkout_note' => $checkoutNote !== '' ? $checkoutNote : null,
            'id' => $contractId
        ]);

        ktxSyncRoomStatus($db, (int) $contract['room_id']);

        ktxAuditSafe($db, (int) $manager['id'], 'update', 'contracts', $contractId, 'active', 'terminated');

        $db->commit();

        redirectTo('manager/contracts');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Checkout / kết thúc hợp đồng thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/contracts">Quay lại</a>';
        exit;
    }
};

$routes['manager/invoices'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $status = trim($_GET['status'] ?? '');
    $allowedStatuses = ['unpaid', 'paid', 'partially_paid', 'overdue', 'cancelled'];

    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, $allowedStatuses, true)) {
        $where = 'WHERE i.status = :status';
        $params['status'] = $status;
    }

    $stmt = $db->prepare("
        SELECT
            i.id,
            i.invoice_code,
            i.month_year,
            i.due_date,
            i.total_amount,
            i.paid_amount,
            i.status,
            i.created_at,
            c.contract_code,
            s.student_code,
            s.full_name,
            r.room_number,
            b.building_name,
            creator.username AS created_by_username,
            COALESCE((
                SELECT SUM(p.amount)
                FROM payments p
                WHERE p.invoice_id = i.id
                  AND p.status = 'pending'
            ), 0) AS pending_amount
        FROM invoices i
        JOIN contracts c ON c.id = i.contract_id
        JOIN students s ON s.id = i.student_id
        JOIN rooms r ON r.id = i.room_id
        JOIN buildings b ON b.id = r.building_id
        LEFT JOIN users creator ON creator.id = i.created_by
        $where
        ORDER BY
            CASE i.status
                WHEN 'overdue' THEN 1
                WHEN 'unpaid' THEN 2
                WHEN 'partially_paid' THEN 3
                WHEN 'paid' THEN 4
                ELSE 5
            END,
            i.due_date ASC,
            i.created_at DESC
    ");

    $stmt->execute($params);

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM invoices")->fetchColumn(),
        'unpaid' => $db->query("SELECT COUNT(*) FROM invoices WHERE status = 'unpaid'")->fetchColumn(),
        'paid' => $db->query("SELECT COUNT(*) FROM invoices WHERE status = 'paid'")->fetchColumn(),
        'overdue' => $db->query("SELECT COUNT(*) FROM invoices WHERE status = 'overdue'")->fetchColumn(),
        'partially_paid' => $db->query("SELECT COUNT(*) FROM invoices WHERE status = 'partially_paid'")->fetchColumn(),
    ];

    render('manager/invoices', [
        'title' => 'Hóa đơn',
        'invoices' => $stmt->fetchAll(),
        'summary' => $summary,
        'currentStatus' => $status
    ]);
};

$routes['manager/invoice-print'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $invoiceId = (int) ($_GET['invoice_id'] ?? 0);

    if ($invoiceId <= 0) {
        redirectTo('manager/invoices');
    }

    $stmt = $db->prepare("
        SELECT
            i.*,
            c.contract_code,
            s.student_code,
            s.full_name,
            s.faculty,
            r.room_number,
            b.building_name,
            creator.username AS created_by_username
        FROM invoices i
        JOIN contracts c ON c.id = i.contract_id
        JOIN students s ON s.id = i.student_id
        JOIN rooms r ON r.id = i.room_id
        JOIN buildings b ON b.id = r.building_id
        LEFT JOIN users creator ON creator.id = i.created_by
        WHERE i.id = :invoice_id
        LIMIT 1
    ");

    $stmt->execute(['invoice_id' => $invoiceId]);
    $invoice = $stmt->fetch();

    if (!$invoice) {
        echo '<h2>Không tìm thấy hóa đơn.</h2>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/invoices">Quay lại</a>';
        return;
    }

    $detailStmt = $db->prepare("
        SELECT
            d.*,
            sv.service_name
        FROM invoice_details d
        LEFT JOIN services sv ON sv.id = d.service_id
        WHERE d.invoice_id = :invoice_id
        ORDER BY d.id
    ");
    $detailStmt->execute(['invoice_id' => $invoiceId]);

    $paymentStmt = $db->prepare("
        SELECT
            payment_code,
            amount,
            payment_method,
            payment_date,
            status
        FROM payments
        WHERE invoice_id = :invoice_id
        ORDER BY payment_date DESC, id DESC
    ");
    $paymentStmt->execute(['invoice_id' => $invoiceId]);

    render('print/invoice', [
        'title' => 'In hóa đơn',
        'invoice' => $invoice,
        'details' => $detailStmt->fetchAll(),
        'payments' => $paymentStmt->fetchAll(),
        'backUrl' => BASE_URL . '/index.php?route=manager/invoices'
    ]);
};

$routes['manager/invoice-create'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $contracts = $db->query("
        SELECT
            c.id,
            c.contract_code,
            c.monthly_price,
            s.student_code,
            s.full_name,
            r.room_number,
            b.building_name
        FROM contracts c
        JOIN students s ON s.id = c.student_id
        JOIN rooms r ON r.id = c.room_id
        JOIN buildings b ON b.id = r.building_id
        WHERE c.status = 'active'
        ORDER BY c.created_at DESC
    ")->fetchAll();

    $services = $db->query("
        SELECT id, service_name
        FROM services
        ORDER BY service_name
    ")->fetchAll();

    render('manager/invoice_create', [
        'title' => 'Tạo hóa đơn',
        'contracts' => $contracts,
        'services' => $services,
        'errors' => [],
        'old' => []
    ]);
};

$routes['manager/invoice-store'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/invoices');
    }

    $manager = Auth::user();

    $contractId = (int) ($_POST['contract_id'] ?? 0);
    $monthYear = trim($_POST['month_year'] ?? '');
    $dueDate = trim($_POST['due_date'] ?? '');
    $includeRoomRent = isset($_POST['include_room_rent']);
    $roomRentAmount = (float) ($_POST['room_rent_amount'] ?? 0);

    $serviceIds = $_POST['service_id'] ?? [];
    $serviceDescriptions = $_POST['service_description'] ?? [];
    $serviceQuantities = $_POST['service_quantity'] ?? [];
    $serviceUnitPrices = $_POST['service_unit_price'] ?? [];

    $errors = [];

    if ($contractId <= 0) {
        $errors[] = 'Vui lòng chọn hợp đồng.';
    }

    if ($monthYear === '') {
        $errors[] = 'Vui lòng chọn tháng lập hóa đơn.';
    }

    if ($dueDate === '') {
        $errors[] = 'Vui lòng chọn hạn thanh toán.';
    }

    if (!empty($errors)) {
        echo '<h2>Create invoice failed</h2>';
        echo '<p>' . htmlspecialchars(implode(' ', $errors)) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/invoice-create">Go back</a>';
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT
                c.*,
                r.id AS room_id
            FROM contracts c
            JOIN rooms r ON r.id = c.room_id
            WHERE c.id = :contract_id
              AND c.status = 'active'
            LIMIT 1
        ");

        $stmt->execute([
            'contract_id' => $contractId
        ]);

        $contract = $stmt->fetch();

        if (!$contract) {
            throw new Exception('Không tìm thấy hợp đồng active.');
        }

        $stmt = $db->prepare("
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

        $details = [];
        $totalAmount = 0;

        if ($includeRoomRent) {
            if ($roomRentAmount <= 0) {
                $roomRentAmount = (float) $contract['monthly_price'];
            }

            $details[] = [
                'service_id' => null,
                'description' => 'Room rent',
                'quantity' => 1,
                'unit_price' => $roomRentAmount,
                'amount' => $roomRentAmount
            ];

            $totalAmount += $roomRentAmount;
        }

        foreach ($serviceIds as $index => $serviceId) {
            $serviceId = (int) $serviceId;
            $description = trim($serviceDescriptions[$index] ?? '');
            $quantity = (float) ($serviceQuantities[$index] ?? 0);
            $unitPrice = (float) ($serviceUnitPrices[$index] ?? 0);

            if ($serviceId <= 0 && $description === '' && $quantity <= 0 && $unitPrice <= 0) {
                continue;
            }

            if ($serviceId <= 0 || $quantity <= 0 || $unitPrice <= 0) {
                continue;
            }

            $amount = $quantity * $unitPrice;

            $details[] = [
                'service_id' => $serviceId,
                'description' => $description !== '' ? $description : 'Service charge',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'amount' => $amount
            ];

            $totalAmount += $amount;
        }

        if ($totalAmount <= 0) {
            throw new Exception('Tổng tiền hóa đơn phải lớn hơn 0.');
        }

        $invoiceCode = 'INV' . date('YmdHis') . $contractId;

        $stmt = $db->prepare("
            INSERT INTO invoices (
                invoice_code,
                contract_id,
                student_id,
                room_id,
                month_year,
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
            'due_date' => $dueDate,
            'total_amount' => $totalAmount,
            'created_by' => $manager['id']
        ]);

        $invoiceId = (int) $db->lastInsertId();

        $stmt = $db->prepare("
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
            $stmt->execute([
                'invoice_id' => $invoiceId,
                'service_id' => $detail['service_id'],
                'description' => $detail['description'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'amount' => $detail['amount']
            ]);
        }

        ktxAuditSafe($db, (int) $manager['id'], 'create', 'invoices', $invoiceId, null, 'Created invoice ' . $invoiceCode);

        $db->commit();

        redirectTo('manager/invoices');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Create invoice failed</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/invoice-create">Go back</a>';
        exit;
    }
};

$routes['manager/payments'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsurePaymentSchema($db);

    $status = trim($_GET['status'] ?? '');
    $allowedStatuses = ['pending', 'success', 'rejected'];

    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, $allowedStatuses, true)) {
        $where = 'WHERE p.status = :status';
        $params['status'] = $status;
    }

    $stmt = $db->prepare("
        SELECT
            p.id,
            p.payment_code,
            p.amount,
            p.payment_method,
            p.sender_bank,
            p.sender_account_name,
            p.payment_date,
            p.status,
            p.transaction_reference,
            p.note,
            p.payment_proof_image,
            p.gateway_status,
            p.gateway_transaction_id,
            p.transfer_content,
            p.verified_at,
            p.rejection_reason,
            i.id AS invoice_id,
            i.invoice_code,
            i.total_amount,
            i.paid_amount,
            i.status AS invoice_status,
            s.student_code,
            s.full_name,
            r.room_number,
            b.building_name,
            verifier.username AS verified_by_username
        FROM payments p
        JOIN invoices i ON i.id = p.invoice_id
        JOIN students s ON s.id = p.student_id
        JOIN rooms r ON r.id = i.room_id
        JOIN buildings b ON b.id = r.building_id
        LEFT JOIN users verifier ON verifier.id = p.verified_by
        $where
        ORDER BY
            CASE p.status
                WHEN 'pending' THEN 1
                WHEN 'success' THEN 2
                WHEN 'rejected' THEN 3
                ELSE 4
            END,
            p.payment_date DESC,
            p.id DESC
    ");

    $stmt->execute($params);

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM payments")->fetchColumn(),
        'pending' => $db->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn(),
        'success' => $db->query("SELECT COUNT(*) FROM payments WHERE status = 'success'")->fetchColumn(),
        'rejected' => $db->query("SELECT COUNT(*) FROM payments WHERE status = 'rejected'")->fetchColumn(),
    ];

    render('manager/payments', [
        'title' => 'Thanh toán',
        'payments' => $stmt->fetchAll(),
        'summary' => $summary,
        'currentStatus' => $status
    ]);
};

$routes['manager/payment-approve'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsurePaymentSchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/payments');
    }

    $manager = Auth::user();
    $paymentId = (int) ($_POST['payment_id'] ?? 0);

    if ($paymentId <= 0) {
        redirectTo('manager/payments');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT
                p.*,
                i.invoice_code,
                i.total_amount,
                i.paid_amount,
                i.status AS invoice_status
            FROM payments p
            JOIN invoices i ON i.id = p.invoice_id
            WHERE p.id = :payment_id
            LIMIT 1
        ");

        $stmt->execute([
            'payment_id' => $paymentId
        ]);

        $payment = $stmt->fetch();

        if (!$payment) {
            throw new Exception('Không tìm thấy payment.');
        }

        if ($payment['status'] !== 'pending') {
            throw new Exception('Payment này đã được xử lý.');
        }

        $newPaidAmount = (float) $payment['paid_amount'] + (float) $payment['amount'];

        if ($newPaidAmount > (float) $payment['total_amount']) {
            $newPaidAmount = (float) $payment['total_amount'];
        }

        $newInvoiceStatus = $newPaidAmount >= (float) $payment['total_amount']
            ? 'paid'
            : 'partially_paid';

        $stmt = $db->prepare("
            UPDATE payments
            SET
                status = 'success',
                payment_status = 'success',
                paid_at = NOW(),
                verified_by = :verified_by,
                verified_at = NOW(),
                rejection_reason = NULL
            WHERE id = :payment_id
        ");

        $stmt->execute([
            'verified_by' => $manager['id'],
            'payment_id' => $paymentId
        ]);

        $stmt = $db->prepare("
            UPDATE invoices
            SET
                paid_amount = :paid_amount,
                status = :status
            WHERE id = :invoice_id
        ");

        $stmt->execute([
            'paid_amount' => $newPaidAmount,
            'status' => $newInvoiceStatus,
            'invoice_id' => $payment['invoice_id']
        ]);

        ktxAuditSafe($db, (int) $manager['id'], 'update', 'payments', $paymentId, 'pending', 'success');

        $db->commit();

        redirectTo('manager/payments');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Approve payment failed</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/payments">Go back</a>';
        exit;
    }
};

$routes['manager/payment-reject'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsurePaymentSchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/payments');
    }

    $manager = Auth::user();
    $paymentId = (int) ($_POST['payment_id'] ?? 0);
    $reason = trim($_POST['rejection_reason'] ?? '');

    if ($paymentId <= 0) {
        redirectTo('manager/payments');
    }

    if ($reason === '') {
        $reason = 'Payment information could not be verified.';
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT *
            FROM payments
            WHERE id = :payment_id
            LIMIT 1
        ");

        $stmt->execute([
            'payment_id' => $paymentId
        ]);

        $payment = $stmt->fetch();

        if (!$payment) {
            throw new Exception('Không tìm thấy payment.');
        }

        if ($payment['status'] !== 'pending') {
            throw new Exception('Payment này đã được xử lý.');
        }

        $stmt = $db->prepare("
            UPDATE payments
            SET
                status = 'rejected',
                payment_status = 'failed',
                verified_by = :verified_by,
                verified_at = NOW(),
                rejection_reason = :reason
            WHERE id = :payment_id
        ");

        $stmt->execute([
            'verified_by' => $manager['id'],
            'reason' => $reason,
            'payment_id' => $paymentId
        ]);

        ktxAuditSafe($db, (int) $manager['id'], 'update', 'payments', $paymentId, 'pending', 'rejected');

        $db->commit();

        redirectTo('manager/payments');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Reject payment failed</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/payments">Go back</a>';
        exit;
    }
};

$routes['manager/maintenance'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $status = trim($_GET['status'] ?? '');
    $allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];

    $where = '';
    $params = [];

    if ($status !== '' && in_array($status, $allowedStatuses, true)) {
        $where = 'WHERE mr.status = :status';
        $params['status'] = $status;
    }

    $stmt = $db->prepare("
        SELECT
            mr.id,
            mr.student_id,
            mr.room_id,
            mr.title,
            mr.description,
            mr.category,
            mr.priority,
            mr.status,
            mr.request_date,
            mr.processed_by,
            mr.processed_at,
            mr.resolution_note,
            mr.evidence_image,
            s.student_code,
            s.full_name,
            r.room_number,
            b.building_name,
            manager.username AS processed_by_username
        FROM maintenance_requests mr
        LEFT JOIN students s ON s.id = mr.student_id
        LEFT JOIN rooms r ON r.id = mr.room_id
        LEFT JOIN buildings b ON b.id = r.building_id
        LEFT JOIN users manager ON manager.id = mr.processed_by
        $where
        ORDER BY
            CASE mr.status
                WHEN 'pending' THEN 1
                WHEN 'in_progress' THEN 2
                WHEN 'completed' THEN 3
                WHEN 'cancelled' THEN 4
                ELSE 5
            END,
            CASE mr.priority
                WHEN 'urgent' THEN 1
                WHEN 'high' THEN 2
                WHEN 'medium' THEN 3
                WHEN 'low' THEN 4
                ELSE 5
            END,
            mr.request_date DESC,
            mr.id DESC
    ");

    $stmt->execute($params);

    $summary = [
        'total' => $db->query("SELECT COUNT(*) FROM maintenance_requests")->fetchColumn(),
        'pending' => $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status = 'pending'")->fetchColumn(),
        'in_progress' => $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status = 'in_progress'")->fetchColumn(),
        'completed' => $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status = 'completed'")->fetchColumn(),
        'cancelled' => $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status = 'cancelled'")->fetchColumn(),
    ];

    render('manager/maintenance', [
        'title' => 'Quản lý sửa chữa',
        'requests' => $stmt->fetchAll(),
        'summary' => $summary,
        'currentStatus' => $status
    ]);
};

$routes['manager/maintenance-update'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/maintenance');
    }

    $manager = Auth::user();
    $requestId = (int) ($_POST['request_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $resolutionNote = trim($_POST['resolution_note'] ?? '');

    $allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];

    if ($requestId <= 0 || !in_array($status, $allowedStatuses, true)) {
        redirectTo('manager/maintenance');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT *
            FROM maintenance_requests
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $requestId
        ]);

        $request = $stmt->fetch();

        if (!$request) {
            throw new Exception('Không tìm thấy yêu cầu sửa chữa.');
        }

        $stmt = $db->prepare("
            UPDATE maintenance_requests
            SET
                status = :status,
                processed_by = :processed_by,
                processed_at = NOW(),
                resolution_note = :resolution_note
            WHERE id = :id
        ");

        $stmt->execute([
            'status' => $status,
            'processed_by' => $manager['id'],
            'resolution_note' => $resolutionNote !== '' ? $resolutionNote : null,
            'id' => $requestId
        ]);

        ktxAuditSafe($db, (int) $manager['id'], 'update', 'maintenance_requests', $requestId, $request['status'], $status);

        $db->commit();

        redirectTo('manager/maintenance');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Cập nhật yêu cầu sửa chữa thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/maintenance">Quay lại</a>';
        exit;
    }
};

$routes['manager/violations'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureContractTerminationSchema($db);

    $violationPointRules = managerViolationPointRules();
    $criticalThreshold = 15;

    $students = $db->query("
        SELECT
            id,
            student_code,
            full_name,
            gender,
            faculty,
            program
        FROM students
        ORDER BY student_code
    ")->fetchAll();

    $violations = $db->query("
        SELECT
            vr.id,
            vr.violation_type,
            vr.description,
            vr.penalty_points,
            vr.violation_date,
            vr.created_at,
            s.student_code,
            s.full_name,
            s.faculty,
            creator.username AS created_by_username
        FROM violation_records vr
        JOIN students s ON s.id = vr.student_id
        LEFT JOIN users creator ON creator.id = vr.recorded_by
        ORDER BY 
            vr.violation_date DESC,
            vr.created_at DESC,
            vr.id DESC
    ")->fetchAll();

    $warningStudents = managerGetWarningStudents($db);
    $summary = managerViolationSummary($db, $warningStudents, $criticalThreshold);

    render('manager/violations', [
        'title' => 'Biên bản vi phạm',
        'students' => $students,
        'violations' => $violations,
        'warningStudents' => $warningStudents,
        'summary' => $summary,
        'violationPointRules' => $violationPointRules,
        'criticalThreshold' => $criticalThreshold,
        'errors' => [],
        'old' => []
    ]);
};

$routes['manager/violation-store'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureContractTerminationSchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/violations');
    }

    $manager = Auth::user();
    $violationPointRules = managerViolationPointRules();
    $criticalThreshold = 15;

    $studentId = (int) ($_POST['student_id'] ?? 0);
    $violationType = trim($_POST['violation_type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $violationDate = trim($_POST['violation_date'] ?? '');
    $customPenaltyPoints = (int) ($_POST['custom_penalty_points'] ?? 0);

    $errors = [];
    $penaltyPoints = 0;

    if ($studentId <= 0) {
        $errors[] = 'Vui lòng chọn sinh viên.';
    }

    if ($violationType === '') {
        $errors[] = 'Vui lòng chọn loại vi phạm.';
    } elseif (!array_key_exists($violationType, $violationPointRules)) {
        $errors[] = 'Loại vi phạm không hợp lệ.';
    } else {
        if ($violationType === 'Other') {
            if ($customPenaltyPoints <= 0) {
                $errors[] = 'Vui lòng nhập điểm phạt cho loại Other.';
            } elseif ($customPenaltyPoints > 20) {
                $errors[] = 'Điểm phạt cho một vi phạm không nên vượt quá 20.';
            } else {
                $penaltyPoints = $customPenaltyPoints;
            }
        } else {
            $penaltyPoints = (int) $violationPointRules[$violationType];
        }
    }

    if ($description === '') {
        $errors[] = 'Vui lòng nhập mô tả vi phạm.';
    }

    if ($violationDate === '') {
        $errors[] = 'Vui lòng chọn ngày vi phạm.';
    }

    if (!empty($errors)) {
        $students = $db->query("
            SELECT
                id,
                student_code,
                full_name,
                gender,
                faculty,
                program
            FROM students
            ORDER BY student_code
        ")->fetchAll();

        $violations = $db->query("
            SELECT
                vr.id,
                vr.violation_type,
                vr.description,
                vr.penalty_points,
                vr.violation_date,
                vr.created_at,
                s.student_code,
                s.full_name,
                s.faculty,
                creator.username AS created_by_username
            FROM violation_records vr
            JOIN students s ON s.id = vr.student_id
            LEFT JOIN users creator ON creator.id = vr.recorded_by
            ORDER BY 
                vr.violation_date DESC,
                vr.created_at DESC,
                vr.id DESC
        ")->fetchAll();

        $warningStudents = managerGetWarningStudents($db);
        $summary = managerViolationSummary($db, $warningStudents, $criticalThreshold);

        render('manager/violations', [
            'title' => 'Biên bản vi phạm',
            'students' => $students,
            'violations' => $violations,
            'warningStudents' => $warningStudents,
            'summary' => $summary,
            'violationPointRules' => $violationPointRules,
            'criticalThreshold' => $criticalThreshold,
            'errors' => $errors,
            'old' => $_POST
        ]);
        return;
    }

    try {
        $stmt = $db->prepare("
            SELECT *
            FROM students
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $studentId
        ]);

        $student = $stmt->fetch();

        if (!$student) {
            throw new Exception('Không tìm thấy sinh viên.');
        }

        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO violation_records (
                student_id,
                violation_type,
                description,
                penalty_points,
                violation_date,
                recorded_by,
                created_at
            )
            VALUES (
                :student_id,
                :violation_type,
                :description,
                :penalty_points,
                :violation_date,
                :recorded_by,
                NOW()
            )
        ");

        $stmt->execute([
            'student_id' => $studentId,
            'violation_type' => $violationType,
            'description' => $description,
            'penalty_points' => $penaltyPoints,
            'violation_date' => $violationDate,
            'recorded_by' => $manager['id']
        ]);

        $violationId = (int) $db->lastInsertId();

        ktxAuditSafe(
            $db,
            (int) $manager['id'],
            'create',
            'violation_records',
            $violationId,
            null,
            'Created violation record for student ' . $student['student_code'] . ' with ' . $penaltyPoints . ' points'
        );

        $db->commit();

        redirectTo('manager/violations');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo biên bản vi phạm thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/violations">Quay lại</a>';
        exit;
    }
};

$routes['manager/violation-terminate-contract'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureContractTerminationSchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/violations');
    }

    $manager = Auth::user();
    $studentId = (int) ($_POST['student_id'] ?? 0);
    $criticalThreshold = 15;

    if ($studentId <= 0) {
        redirectTo('manager/violations');
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            SELECT
                s.id,
                s.student_code,
                s.full_name,
                COALESCE(SUM(vr.penalty_points), 0) AS total_points
            FROM students s
            LEFT JOIN violation_records vr ON vr.student_id = s.id
            WHERE s.id = :student_id
            GROUP BY s.id, s.student_code, s.full_name
            LIMIT 1
        ");

        $stmt->execute([
            'student_id' => $studentId
        ]);

        $student = $stmt->fetch();

        if (!$student) {
            throw new Exception('Không tìm thấy sinh viên.');
        }

        $totalPoints = (int) $student['total_points'];

        if ($totalPoints < $criticalThreshold) {
            throw new Exception('Sinh viên chưa đạt Critical Warning nên chưa thể chấm dứt hợp đồng.');
        }

        $stmt = $db->prepare("
            SELECT *
            FROM contracts
            WHERE student_id = :student_id
              AND status = 'active'
        ");

        $stmt->execute([
            'student_id' => $studentId
        ]);

        $activeContracts = $stmt->fetchAll();

        if (empty($activeContracts)) {
            throw new Exception('Sinh viên này không có hợp đồng active để chấm dứt.');
        }

        foreach ($activeContracts as $contract) {
            $note = 'Terminated due to Critical Warning. Total violation points: ' . $totalPoints;

            $stmt = $db->prepare("
                UPDATE contracts
                SET
                    status = 'terminated',
                    ended_at = NOW(),
                    ended_by = :ended_by,
                    checkout_note = :checkout_note
                WHERE id = :id
            ");

            $stmt->execute([
                'ended_by' => $manager['id'],
                'checkout_note' => $note,
                'id' => $contract['id']
            ]);

            ktxSyncRoomStatus($db, (int) $contract['room_id']);

            ktxAuditSafe(
                $db,
                (int) $manager['id'],
                'update',
                'contracts',
                (int) $contract['id'],
                'active',
                'terminated due to Critical Warning for student ' . $student['student_code']
            );
        }

        $db->commit();

        redirectTo('manager/violations');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Chấm dứt hợp đồng do vi phạm thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/violations">Quay lại</a>';
        exit;
    }
};

$routes['manager/utility-readings'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureUtilitySchema($db);

    $rooms = $db->query("
        SELECT
            r.id,
            r.room_number,
            r.status,
            b.building_name
        FROM rooms r
        JOIN buildings b ON b.id = r.building_id
        WHERE r.status IN ('available', 'full')
        ORDER BY b.building_name, r.room_number
    ")->fetchAll();

    $services = $db->query("
        SELECT
            id,
            service_name,
            unit,
            default_price
        FROM services
        WHERE status = 'active'
        ORDER BY service_name
    ")->fetchAll();

    $semesters = $db->query("
        SELECT
            id,
            semester_name
        FROM semesters
        ORDER BY start_date DESC, id DESC
    ")->fetchAll();

    $stmt = $db->query("
        SELECT
            ur.id,
            ur.room_id,
            ur.service_id,
            ur.semester_id,
            ur.reading_month,
            ur.previous_reading,
            ur.current_reading,
            ur.consumption,
            ur.unit_price,
            ur.total_amount,
            ur.invoice_id,
            ur.recorded_by,
            ur.recorded_at,
            ur.status,
            r.room_number,
            b.building_name,
            COALESCE(sv.service_name, 'Unknown service') AS service_name,
            COALESCE(sv.unit, '-') AS unit,
            sem.semester_name,
            u.username AS recorded_by_username
        FROM utility_readings ur
        JOIN rooms r ON r.id = ur.room_id
        JOIN buildings b ON b.id = r.building_id
        LEFT JOIN services sv ON sv.id = ur.service_id
        LEFT JOIN semesters sem ON sem.id = ur.semester_id
        LEFT JOIN users u ON u.id = ur.recorded_by
        ORDER BY ur.id DESC
    ");

    render('manager/utility_readings', [
        'title' => 'Chỉ số điện nước',
        'rooms' => $rooms,
        'services' => $services,
        'semesters' => $semesters,
        'readings' => $stmt->fetchAll(),
        'errors' => [],
        'old' => []
    ]);
};

$routes['manager/utility-reading-store'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureUtilitySchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/utility-readings');
    }

    $manager = Auth::user();

    $roomId = (int) ($_POST['room_id'] ?? 0);
    $serviceId = (int) ($_POST['service_id'] ?? 0);
    $semesterId = (int) ($_POST['semester_id'] ?? 0);
    $readingMonth = managerNormalizeReadingMonth((string) ($_POST['reading_month'] ?? ''));
    $previousReading = (float) ($_POST['previous_reading'] ?? 0);
    $currentReading = (float) ($_POST['current_reading'] ?? 0);
    $unitPrice = (float) ($_POST['unit_price'] ?? 0);
    $autoGenerateInvoice = isset($_POST['auto_generate_invoice']);

    $errors = [];

    if ($roomId <= 0) {
        $errors[] = 'Vui lòng chọn phòng.';
    }

    if ($serviceId <= 0) {
        $errors[] = 'Vui lòng chọn dịch vụ.';
    }

    if ($readingMonth === null) {
        $errors[] = 'Vui lòng chọn tháng ghi chỉ số.';
    }

    if ($currentReading < $previousReading) {
        $errors[] = 'Chỉ số mới không được nhỏ hơn chỉ số cũ.';
    }

    if ($unitPrice < 0) {
        $errors[] = 'Đơn giá không hợp lệ.';
    }

    if (!empty($errors)) {
        echo '<h2>Tạo utility reading thất bại</h2>';

        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }

        echo '<a href="' . BASE_URL . '/index.php?route=manager/utility-readings">Quay lại</a>';
        exit;
    }

    $consumption = $currentReading - $previousReading;
    $totalAmount = $consumption * $unitPrice;

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO utility_readings (
                room_id,
                service_id,
                semester_id,
                reading_month,
                previous_reading,
                current_reading,
                consumption,
                unit_price,
                total_amount,
                recorded_by,
                recorded_at,
                status
            )
            VALUES (
                :room_id,
                :service_id,
                :semester_id,
                :reading_month,
                :previous_reading,
                :current_reading,
                :consumption,
                :unit_price,
                :total_amount,
                :recorded_by,
                NOW(),
                'recorded'
            )
        ");

        $stmt->execute([
            'room_id' => $roomId,
            'service_id' => $serviceId,
            'semester_id' => $semesterId > 0 ? $semesterId : null,
            'reading_month' => $readingMonth,
            'previous_reading' => $previousReading,
            'current_reading' => $currentReading,
            'consumption' => $consumption,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'recorded_by' => $manager['id']
        ]);

        $readingId = (int) $db->lastInsertId();

        ktxAuditSafe($db, (int) $manager['id'], 'create', 'utility_readings', $readingId, null, 'Created utility reading');

        if ($autoGenerateInvoice) {
            managerGenerateUtilityInvoicesFromReading($db, $readingId, (int) $manager['id']);
        }

        $db->commit();

        redirectTo('manager/utility-readings');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Tạo utility reading thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/utility-readings">Quay lại</a>';
        exit;
    }
};

$routes['manager/utility-generate-invoice'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    managerEnsureUtilitySchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('manager/utility-readings');
    }

    $manager = Auth::user();
    $readingId = (int) ($_POST['reading_id'] ?? 0);

    if ($readingId <= 0) {
        redirectTo('manager/utility-readings');
    }

    try {
        $db->beginTransaction();
        managerGenerateUtilityInvoicesFromReading($db, $readingId, (int) $manager['id']);
        $db->commit();

        redirectTo('manager/utility-readings');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Sinh hóa đơn từ utility reading thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=manager/utility-readings">Quay lại</a>';
        exit;
    }
};
