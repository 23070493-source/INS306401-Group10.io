<?php

if (!function_exists('studentColumnExists')) {
    function studentColumnExists(PDO $db, string $tableName, string $columnName): bool
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

if (!function_exists('studentAddColumnIfMissing')) {
    function studentAddColumnIfMissing(PDO $db, string $tableName, string $columnName, string $columnDefinition): void
    {
        if (!studentColumnExists($db, $tableName, $columnName)) {
            $db->exec("ALTER TABLE {$tableName} ADD COLUMN {$columnDefinition}");
        }
    }
}

if (!function_exists('studentEnsureMaintenanceSchema')) {
    function studentEnsureMaintenanceSchema(PDO $db): void
    {
        studentAddColumnIfMissing($db, 'maintenance_requests', 'evidence_image', 'evidence_image VARCHAR(255) NULL');
    }
}

if (!function_exists('studentStoreMaintenanceEvidence')) {
    function studentStoreMaintenanceEvidence(array &$errors): ?string
    {
        if (empty($_FILES['evidence_image']) || ($_FILES['evidence_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES['evidence_image'];

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors[] = 'Không tải được ảnh minh chứng. Vui lòng chọn lại ảnh.';
            return null;
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            $errors[] = 'Ảnh minh chứng không được vượt quá 5MB.';
            return null;
        }

        $mimeType = '';

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = (string) finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
            }
        }

        if ($mimeType === '' && function_exists('mime_content_type')) {
            $mimeType = (string) mime_content_type($file['tmp_name']);
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedMimeTypes[$mimeType])) {
            $errors[] = 'Ảnh minh chứng chỉ nhận JPG, PNG hoặc WEBP.';
            return null;
        }

        $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'maintenance';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            $errors[] = 'Không tạo được thư mục lưu ảnh minh chứng.';
            return null;
        }

        $fileName = 'maintenance_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowedMimeTypes[$mimeType];
        $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $errors[] = 'Không lưu được ảnh minh chứng. Vui lòng thử lại.';
            return null;
        }

        return 'uploads/maintenance/' . $fileName;
    }
}

$routes['student/dashboard'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute([
        'user_id' => $user['id']
    ]);
    $student = $stmt->fetch();

    $registration = null;
    $contract = null;
    $unpaidInvoices = [];
    $maintenanceRequests = [];
    $violationPoints = 0;

    if ($student) {
        $stmt = $db->prepare("
            SELECT 
                rr.*,
                se.semester_name,
                b.building_name AS desired_building
            FROM room_registrations rr
            JOIN semesters se ON se.id = rr.semester_id
            LEFT JOIN buildings b ON b.id = rr.desired_building_id
            WHERE rr.student_id = :student_id
            ORDER BY rr.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([
            'student_id' => $student['id']
        ]);
        $registration = $stmt->fetch();

        $stmt = $db->prepare("
            SELECT 
                c.*,
                r.room_number,
                b.building_name
            FROM contracts c
            JOIN rooms r ON r.id = c.room_id
            JOIN buildings b ON b.id = r.building_id
            WHERE c.student_id = :student_id
              AND c.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([
            'student_id' => $student['id']
        ]);
        $contract = $stmt->fetch();

        $stmt = $db->prepare("
            SELECT *
            FROM invoices
            WHERE student_id = :student_id
              AND status IN ('unpaid', 'partially_paid', 'overdue')
            ORDER BY due_date ASC
        ");
        $stmt->execute([
            'student_id' => $student['id']
        ]);
        $unpaidInvoices = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT *
            FROM maintenance_requests
            WHERE student_id = :student_id
            ORDER BY request_date DESC
            LIMIT 5
        ");
        $stmt->execute([
            'student_id' => $student['id']
        ]);
        $maintenanceRequests = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT COALESCE(SUM(penalty_points), 0)
            FROM violation_records
            WHERE student_id = :student_id
        ");
        $stmt->execute([
            'student_id' => $student['id']
        ]);
        $violationPoints = $stmt->fetchColumn();
    }

    render('student/dashboard', [
        'title' => 'Bảng điều khiển sinh viên',
        'student' => $student,
        'registration' => $registration,
        'contract' => $contract,
        'unpaidInvoices' => $unpaidInvoices,
        'maintenanceRequests' => $maintenanceRequests,
        'violationPoints' => $violationPoints
    ]);
};

$routes['student/rooms'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $stmt = $db->query("
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
        ORDER BY b.building_name, r.room_number
    ");

    $rooms = $stmt->fetchAll();

    render('student/rooms', [
        'title' => 'Phòng còn trống',
        'rooms' => $rooms
    ]);
};

$routes['student/register-room'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user['id']
    ]);

    $student = $stmt->fetch();

    $buildings = [];
    $semesters = [];
    $activeContract = null;
    $currentRegistration = null;
    $canRegister = true;

    if (!$student) {
        render('student/register_room', [
            'title' => 'Đăng ký phòng',
            'student' => null,
            'buildings' => [],
            'semesters' => [],
            'activeContract' => null,
            'currentRegistration' => null,
            'canRegister' => false,
            'errors' => ['Không tìm thấy hồ sơ sinh viên.'],
            'success' => null,
            'old' => []
        ]);
        return;
    }

    $buildings = $db->query("
        SELECT id, building_name
        FROM buildings
        ORDER BY building_name
    ")->fetchAll();

    $semesters = $db->query("
        SELECT id, semester_name, academic_year
        FROM semesters
        WHERE status = 'open'
        ORDER BY start_date DESC
    ")->fetchAll();

    $stmt = $db->prepare("
        SELECT
            c.*,
            r.room_number,
            r.room_type,
            r.gender_type,
            b.building_name,
            se.semester_name,
            se.academic_year
        FROM contracts c
        JOIN rooms r ON r.id = c.room_id
        JOIN buildings b ON b.id = r.building_id
        LEFT JOIN semesters se ON se.id = c.semester_id
        WHERE c.student_id = :student_id
          AND c.status = 'active'
        ORDER BY c.id DESC
        LIMIT 1
    ");

    $stmt->execute([
        'student_id' => $student['id']
    ]);

    $activeContract = $stmt->fetch();

    $stmt = $db->prepare("
        SELECT
            rr.*,
            se.semester_name,
            se.academic_year,
            desired_b.building_name AS desired_building,
            assigned_r.room_number AS assigned_room,
            assigned_b.building_name AS assigned_building,
            manager.username AS processed_by_username
        FROM room_registrations rr
        JOIN semesters se ON se.id = rr.semester_id
        LEFT JOIN buildings desired_b ON desired_b.id = rr.desired_building_id
        LEFT JOIN rooms assigned_r ON assigned_r.id = rr.assigned_room_id
        LEFT JOIN buildings assigned_b ON assigned_b.id = assigned_r.building_id
        LEFT JOIN users manager ON manager.id = rr.processed_by
        WHERE rr.student_id = :student_id
          AND rr.status IN ('pending', 'approved')
        ORDER BY rr.id DESC
        LIMIT 1
    ");

    $stmt->execute([
        'student_id' => $student['id']
    ]);

    $currentRegistration = $stmt->fetch();

    if ($activeContract || $currentRegistration) {
        $canRegister = false;
    }

    if (!$canRegister) {
        render('student/register_room', [
            'title' => 'Đăng ký phòng',
            'student' => $student,
            'buildings' => $buildings,
            'semesters' => $semesters,
            'activeContract' => $activeContract,
            'currentRegistration' => $currentRegistration,
            'canRegister' => false,
            'errors' => [],
            'success' => null,
            'old' => []
        ]);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $semesterId = (int) ($_POST['semester_id'] ?? 0);

        $desiredBuildingId = ($_POST['desired_building_id'] ?? '') !== ''
            ? (int) $_POST['desired_building_id']
            : null;

        $desiredRoomType = trim($_POST['desired_room_type'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $errors = [];

        if ($semesterId <= 0) {
            $errors[] = 'Vui lòng chọn học kỳ.';
        }

        if ($desiredRoomType === '') {
            $errors[] = 'Vui lòng chọn loại phòng mong muốn.';
        }

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM contracts
            WHERE student_id = :student_id
              AND status = 'active'
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Bạn đang có hợp đồng KTX active nên không thể đăng ký phòng mới.';
        }

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM room_registrations
            WHERE student_id = :student_id
              AND status IN ('pending', 'approved')
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Bạn đã có đơn đăng ký đang được xử lý hoặc đã được duyệt.';
        }

        if ($semesterId > 0) {
            $stmt = $db->prepare("
                SELECT COUNT(*)
                FROM room_registrations
                WHERE student_id = :student_id
                  AND semester_id = :semester_id
                  AND status IN ('pending', 'approved')
            ");

            $stmt->execute([
                'student_id' => $student['id'],
                'semester_id' => $semesterId
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'Bạn đã có đơn đăng ký trong học kỳ này.';
            }
        }

        if (!empty($errors)) {
            render('student/register_room', [
                'title' => 'Đăng ký phòng',
                'student' => $student,
                'buildings' => $buildings,
                'semesters' => $semesters,
                'activeContract' => null,
                'currentRegistration' => null,
                'canRegister' => true,
                'errors' => $errors,
                'success' => null,
                'old' => $_POST
            ]);
            return;
        }

        $priorityScore = 0;

        if ($student['priority_type'] === 'freshman') {
            $priorityScore = 20;
        } elseif ($student['priority_type'] === 'international') {
            $priorityScore = 30;
        } elseif ($student['priority_type'] === 'policy') {
            $priorityScore = 40;
        } elseif ($student['priority_type'] === 'scholarship') {
            $priorityScore = 25;
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO room_registrations (
                    student_id,
                    semester_id,
                    desired_building_id,
                    desired_room_type,
                    desired_gender_type,
                    assigned_room_id,
                    priority_score,
                    note,
                    status,
                    processed_by,
                    processed_at,
                    rejection_reason
                )
                VALUES (
                    :student_id,
                    :semester_id,
                    :desired_building_id,
                    :desired_room_type,
                    :desired_gender_type,
                    NULL,
                    :priority_score,
                    :note,
                    'pending',
                    NULL,
                    NULL,
                    NULL
                )
            ");

            $stmt->execute([
                'student_id' => $student['id'],
                'semester_id' => $semesterId,
                'desired_building_id' => $desiredBuildingId,
                'desired_room_type' => $desiredRoomType,
                'desired_gender_type' => $student['gender'],
                'priority_score' => $priorityScore,
                'note' => $note !== '' ? $note : null
            ]);

            $registrationId = (int) $db->lastInsertId();

            $db->commit();

            $stmt = $db->prepare("
                SELECT
                    rr.*,
                    se.semester_name,
                    se.academic_year,
                    desired_b.building_name AS desired_building,
                    assigned_r.room_number AS assigned_room,
                    assigned_b.building_name AS assigned_building,
                    manager.username AS processed_by_username
                FROM room_registrations rr
                JOIN semesters se ON se.id = rr.semester_id
                LEFT JOIN buildings desired_b ON desired_b.id = rr.desired_building_id
                LEFT JOIN rooms assigned_r ON assigned_r.id = rr.assigned_room_id
                LEFT JOIN buildings assigned_b ON assigned_b.id = assigned_r.building_id
                LEFT JOIN users manager ON manager.id = rr.processed_by
                WHERE rr.id = :id
                LIMIT 1
            ");

            $stmt->execute([
                'id' => $registrationId
            ]);

            $currentRegistration = $stmt->fetch();

            render('student/register_room', [
                'title' => 'Đăng ký phòng',
                'student' => $student,
                'buildings' => $buildings,
                'semesters' => $semesters,
                'activeContract' => null,
                'currentRegistration' => $currentRegistration,
                'canRegister' => false,
                'errors' => [],
                'success' => 'Gửi đơn đăng ký phòng thành công. Trạng thái hiện tại: pending.',
                'old' => []
            ]);
            return;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            render('student/register_room', [
                'title' => 'Đăng ký phòng',
                'student' => $student,
                'buildings' => $buildings,
                'semesters' => $semesters,
                'activeContract' => null,
                'currentRegistration' => null,
                'canRegister' => true,
                'errors' => ['Gửi đơn đăng ký thất bại: ' . $e->getMessage()],
                'success' => null,
                'old' => $_POST
            ]);
            return;
        }
    }

    render('student/register_room', [
        'title' => 'Đăng ký phòng',
        'student' => $student,
        'buildings' => $buildings,
        'semesters' => $semesters,
        'activeContract' => $activeContract,
        'currentRegistration' => $currentRegistration,
        'canRegister' => $canRegister,
        'errors' => [],
        'success' => null,
        'old' => []
    ]);
};

$routes['student/my-registration'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute([
        'user_id' => $user['id']
    ]);
    $student = $stmt->fetch();

    $registrations = [];

    if ($student) {
        $stmt = $db->prepare("
            SELECT 
                rr.id,
                rr.status,
                rr.priority_score,
                rr.note,
                rr.processed_at,
                rr.rejection_reason,
                rr.created_at,
                se.semester_name,
                se.academic_year,
                desired_b.building_name AS desired_building,
                rr.desired_room_type,
                rr.desired_gender_type,
                assigned_r.room_number AS assigned_room,
                assigned_b.building_name AS assigned_building,
                manager.username AS processed_by
            FROM room_registrations rr
            JOIN semesters se ON se.id = rr.semester_id
            LEFT JOIN buildings desired_b ON desired_b.id = rr.desired_building_id
            LEFT JOIN rooms assigned_r ON assigned_r.id = rr.assigned_room_id
            LEFT JOIN buildings assigned_b ON assigned_b.id = assigned_r.building_id
            LEFT JOIN users manager ON manager.id = rr.processed_by
            WHERE rr.student_id = :student_id
            ORDER BY rr.created_at DESC
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);
        $registrations = $stmt->fetchAll();
    }

    render('student/my_registration', [
        'title' => 'Đơn đăng ký của tôi',
        'student' => $student,
        'registrations' => $registrations
    ]);
};

$routes['student/my-contract'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user['id']
    ]);
    $student = $stmt->fetch();

    $contracts = [];

    if ($student) {
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
                r.room_number,
                r.room_type,
                r.gender_type,
                r.capacity,
                b.building_name,
                se.semester_name,
                se.academic_year,
                creator.username AS created_by_username
            FROM contracts c
            JOIN rooms r ON r.id = c.room_id
            JOIN buildings b ON b.id = r.building_id
            JOIN semesters se ON se.id = c.semester_id
            LEFT JOIN users creator ON creator.id = c.created_by
            WHERE c.student_id = :student_id
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

        $stmt->execute([
            'student_id' => $student['id']
        ]);
        $contracts = $stmt->fetchAll();
    }

    render('student/my_contract', [
        'title' => 'Hợp đồng của tôi',
        'student' => $student,
        'contracts' => $contracts
    ]);
};

$routes['student/my-invoices'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user['id']
    ]);

    $student = $stmt->fetch();

    $invoices = [];

    if ($student) {
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
                r.room_number,
                b.building_name,
                COALESCE((
                    SELECT SUM(p.amount)
                    FROM payments p
                    WHERE p.invoice_id = i.id
                      AND p.status = 'pending'
                ), 0) AS pending_amount
            FROM invoices i
            JOIN contracts c ON c.id = i.contract_id
            JOIN rooms r ON r.id = i.room_id
            JOIN buildings b ON b.id = r.building_id
            WHERE i.student_id = :student_id
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

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        $invoices = $stmt->fetchAll();
    }

    render('student/my_invoices', [
        'title' => 'Hóa đơn của tôi',
        'student' => $student,
        'invoices' => $invoices
    ]);
};

$routes['student/contract-print'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();
    $contractId = (int) ($_GET['contract_id'] ?? 0);

    if ($contractId <= 0) {
        redirectTo('student/my-contract');
    }

    $stmt = $db->prepare("
        SELECT id
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute(['user_id' => $user['id']]);
    $studentId = (int) $stmt->fetchColumn();

    if ($studentId <= 0) {
        redirectTo('student/my-contract');
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
          AND c.student_id = :student_id
        LIMIT 1
    ");

    $stmt->execute([
        'contract_id' => $contractId,
        'student_id' => $studentId
    ]);

    $contract = $stmt->fetch();

    if (!$contract) {
        echo '<h2>Không tìm thấy hợp đồng.</h2>';
        echo '<a href="' . BASE_URL . '/index.php?route=student/my-contract">Quay lại</a>';
        return;
    }

    render('print/contract', [
        'title' => 'In hợp đồng',
        'contract' => $contract,
        'backUrl' => BASE_URL . '/index.php?route=student/my-contract'
    ]);
};

$routes['student/invoice-print'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();
    $invoiceId = (int) ($_GET['invoice_id'] ?? 0);

    if ($invoiceId <= 0) {
        redirectTo('student/my-invoices');
    }

    $stmt = $db->prepare("
        SELECT id
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute(['user_id' => $user['id']]);
    $studentId = (int) $stmt->fetchColumn();

    if ($studentId <= 0) {
        redirectTo('student/my-invoices');
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
          AND i.student_id = :student_id
        LIMIT 1
    ");

    $stmt->execute([
        'invoice_id' => $invoiceId,
        'student_id' => $studentId
    ]);

    $invoice = $stmt->fetch();

    if (!$invoice) {
        echo '<h2>Không tìm thấy hóa đơn.</h2>';
        echo '<a href="' . BASE_URL . '/index.php?route=student/my-invoices">Quay lại</a>';
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
        'backUrl' => BASE_URL . '/index.php?route=student/my-invoices'
    ]);
};

$routes['student/payment-submit'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();
    $invoiceId = (int) ($_GET['invoice_id'] ?? 0);

    if ($invoiceId <= 0) {
        redirectTo('student/my-invoices');
    }

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user['id']
    ]);

    $student = $stmt->fetch();

    if (!$student) {
        echo '<h2>Không tìm thấy hồ sơ sinh viên.</h2>';
        return;
    }

    $stmt = $db->prepare("
        SELECT
            i.*,
            c.contract_code,
            r.room_number,
            b.building_name,
            COALESCE((
                SELECT SUM(p.amount)
                FROM payments p
                WHERE p.invoice_id = i.id
                  AND p.status = 'pending'
            ), 0) AS pending_amount
        FROM invoices i
        JOIN contracts c ON c.id = i.contract_id
        JOIN rooms r ON r.id = i.room_id
        JOIN buildings b ON b.id = r.building_id
        WHERE i.id = :invoice_id
          AND i.student_id = :student_id
        LIMIT 1
    ");

    $stmt->execute([
        'invoice_id' => $invoiceId,
        'student_id' => $student['id']
    ]);

    $invoice = $stmt->fetch();

    if (!$invoice) {
        echo '<h2>Không tìm thấy hóa đơn.</h2>';
        return;
    }

    if ($invoice['status'] === 'paid') {
        echo '<h2>Hóa đơn này đã được thanh toán.</h2>';
        echo '<a href="' . BASE_URL . '/index.php?route=student/my-invoices">Quay lại</a>';
        return;
    }

    if ((float) $invoice['pending_amount'] > 0) {
        echo '<h2>Bạn đã gửi thanh toán cho hóa đơn này.</h2>';
        echo '<p>Vui lòng chờ Manager xác nhận.</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=student/my-invoices">Quay lại</a>';
        return;
    }

    render('student/payment_submit', [
        'title' => 'Gửi thông tin chuyển khoản',
        'student' => $student,
        'invoice' => $invoice,
        'errors' => [],
        'old' => []
    ]);
};

$routes['student/payment-store'] = function (PDO $db): void {
    Auth::requireRole('Student');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('student/my-invoices');
    }

    $user = Auth::user();

    $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
    $senderBank = trim($_POST['sender_bank'] ?? '');
    $senderAccountName = trim($_POST['sender_account_name'] ?? '');
    $transactionReference = trim($_POST['transaction_reference'] ?? '');
    $note = trim($_POST['note'] ?? '');

    $errors = [];

    if ($invoiceId <= 0) {
        $errors[] = 'Hóa đơn không hợp lệ.';
    }

    if ($senderBank === '') {
        $errors[] = 'Vui lòng nhập ngân hàng đã chuyển.';
    }

    if ($senderAccountName === '') {
        $errors[] = 'Vui lòng nhập tên chủ tài khoản chuyển.';
    }

    if ($transactionReference === '') {
        $errors[] = 'Vui lòng nhập mã giao dịch.';
    }

    try {
        $stmt = $db->prepare("
            SELECT *
            FROM students
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $user['id']
        ]);

        $student = $stmt->fetch();

        if (!$student) {
            throw new Exception('Không tìm thấy hồ sơ sinh viên.');
        }

        $stmt = $db->prepare("
            SELECT
                i.*,
                c.contract_code,
                r.room_number,
                b.building_name,
                COALESCE((
                    SELECT SUM(p.amount)
                    FROM payments p
                    WHERE p.invoice_id = i.id
                      AND p.status = 'pending'
                ), 0) AS pending_amount
            FROM invoices i
            JOIN contracts c ON c.id = i.contract_id
            JOIN rooms r ON r.id = i.room_id
            JOIN buildings b ON b.id = r.building_id
            WHERE i.id = :invoice_id
              AND i.student_id = :student_id
            LIMIT 1
        ");

        $stmt->execute([
            'invoice_id' => $invoiceId,
            'student_id' => $student['id']
        ]);

        $invoice = $stmt->fetch();

        if (!$invoice) {
            throw new Exception('Không tìm thấy hóa đơn.');
        }

        if ($invoice['status'] === 'paid') {
            throw new Exception('Hóa đơn này đã được thanh toán.');
        }

        if ((float) $invoice['pending_amount'] > 0) {
            throw new Exception('Bạn đã gửi thanh toán cho hóa đơn này. Vui lòng chờ Manager xác nhận.');
        }

        if (!empty($errors)) {
            render('student/payment_submit', [
                'title' => 'Gửi thông tin chuyển khoản',
                'student' => $student,
                'invoice' => $invoice,
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $remainingAmount = (float) $invoice['total_amount'] - (float) $invoice['paid_amount'];

        if ($remainingAmount <= 0) {
            throw new Exception('Hóa đơn không còn số tiền cần thanh toán.');
        }

        $db->beginTransaction();

        $paymentCode = 'PAY' . date('YmdHis') . $invoiceId;
        $paymentDateSql = date('Y-m-d H:i:s');

        $stmt = $db->prepare("
            INSERT INTO payments (
                payment_code,
                invoice_id,
                student_id,
                amount,
                payment_method,
                sender_bank,
                sender_account_name,
                payment_date,
                payment_status,
                status,
                transaction_reference,
                note
            )
            VALUES (
                :payment_code,
                :invoice_id,
                :student_id,
                :amount,
                'bank_transfer',
                :sender_bank,
                :sender_account_name,
                :payment_date,
                'pending',
                'pending',
                :transaction_reference,
                :note
            )
        ");

        $stmt->execute([
            'payment_code' => $paymentCode,
            'invoice_id' => $invoiceId,
            'student_id' => $student['id'],
            'amount' => $remainingAmount,
            'sender_bank' => $senderBank,
            'sender_account_name' => $senderAccountName,
            'payment_date' => $paymentDateSql,
            'transaction_reference' => $transactionReference,
            'note' => $note !== '' ? $note : null
        ]);

        $paymentId = $db->lastInsertId();

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
                'create',
                'payments',
                :record_id,
                NULL,
                :new_value,
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $user['id'],
            'record_id' => $paymentId,
            'new_value' => 'Student submitted bank transfer proof for invoice ' . $invoice['invoice_code'],
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);

        $db->commit();

        redirectTo('student/my-invoices');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Gửi thông tin chuyển khoản thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=student/my-invoices">Quay lại</a>';
        exit;
    }
};

$routes['student/pay-invoice'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $invoiceId = (int) ($_POST['invoice_id'] ?? $_GET['invoice_id'] ?? 0);

    if ($invoiceId <= 0) {
        redirectTo('student/my-invoices');
    }

    header('Location: ' . BASE_URL . '/index.php?route=student/payment-submit&invoice_id=' . $invoiceId);
    exit;
};
$routes['student/maintenance'] = function (PDO $db): void {
    Auth::requireRole('Student');

    studentEnsureMaintenanceSchema($db);

    $user = Auth::user();

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user['id']
    ]);

    $student = $stmt->fetch();

    $contract = null;
    $requests = [];

    if ($student) {
        $stmt = $db->prepare("
            SELECT 
                c.*,
                r.room_number,
                b.building_name
            FROM contracts c
            JOIN rooms r ON r.id = c.room_id
            JOIN buildings b ON b.id = r.building_id
            WHERE c.student_id = :student_id
              AND c.status = 'active'
            LIMIT 1
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        $contract = $stmt->fetch();

        $stmt = $db->prepare("
            SELECT 
                mr.*,
                r.room_number,
                b.building_name,
                manager.username AS processed_by_username
            FROM maintenance_requests mr
            LEFT JOIN rooms r ON r.id = mr.room_id
            LEFT JOIN buildings b ON b.id = r.building_id
            LEFT JOIN users manager ON manager.id = mr.processed_by
            WHERE mr.student_id = :student_id
            ORDER BY 
                CASE mr.status
                    WHEN 'pending' THEN 1
                    WHEN 'in_progress' THEN 2
                    WHEN 'completed' THEN 3
                    WHEN 'cancelled' THEN 4
                    ELSE 5
                END,
                mr.request_date DESC,
                mr.id DESC
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        $requests = $stmt->fetchAll();
    }

    render('student/maintenance', [
        'title' => 'Yêu cầu sửa chữa',
        'student' => $student,
        'contract' => $contract,
        'requests' => $requests,
        'errors' => [],
        'old' => []
    ]);
};

$routes['student/maintenance-store'] = function (PDO $db): void {
    Auth::requireRole('Student');

    studentEnsureMaintenanceSchema($db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('student/maintenance');
    }

    $user = Auth::user();

    $category = trim($_POST['category'] ?? '');
    $priority = trim($_POST['priority'] ?? 'medium');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $errors = [];

    if ($category === '') {
        $errors[] = 'Vui lòng chọn loại sự cố.';
    }

    if (!in_array($priority, ['low', 'medium', 'high', 'urgent'], true)) {
        $errors[] = 'Mức độ ưu tiên không hợp lệ.';
    }

    if ($title === '') {
        $errors[] = 'Vui lòng nhập tiêu đề yêu cầu.';
    }

    if ($description === '') {
        $errors[] = 'Vui lòng mô tả chi tiết sự cố.';
    }

    try {
        $stmt = $db->prepare("
            SELECT *
            FROM students
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $user['id']
        ]);

        $student = $stmt->fetch();

        if (!$student) {
            throw new Exception('Không tìm thấy hồ sơ sinh viên.');
        }

        $stmt = $db->prepare("
            SELECT 
                c.*,
                r.room_number,
                b.building_name
            FROM contracts c
            JOIN rooms r ON r.id = c.room_id
            JOIN buildings b ON b.id = r.building_id
            WHERE c.student_id = :student_id
              AND c.status = 'active'
            LIMIT 1
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        $contract = $stmt->fetch();

        if (!$contract) {
            throw new Exception('Bạn cần có hợp đồng active trước khi gửi yêu cầu sửa chữa.');
        }

        $evidenceImage = null;

        if (empty($errors)) {
            $evidenceImage = studentStoreMaintenanceEvidence($errors);
        }

        if (!empty($errors)) {
            $stmt = $db->prepare("
                SELECT 
                    mr.*,
                    r.room_number,
                    b.building_name,
                    manager.username AS processed_by_username
                FROM maintenance_requests mr
                LEFT JOIN rooms r ON r.id = mr.room_id
                LEFT JOIN buildings b ON b.id = r.building_id
                LEFT JOIN users manager ON manager.id = mr.processed_by
                WHERE mr.student_id = :student_id
                ORDER BY 
                    CASE mr.status
                        WHEN 'pending' THEN 1
                        WHEN 'in_progress' THEN 2
                        WHEN 'completed' THEN 3
                        WHEN 'cancelled' THEN 4
                        ELSE 5
                    END,
                    mr.request_date DESC,
                    mr.id DESC
            ");

            $stmt->execute([
                'student_id' => $student['id']
            ]);

            $requests = $stmt->fetchAll();

            render('student/maintenance', [
                'title' => 'Yêu cầu sửa chữa',
                'student' => $student,
                'contract' => $contract,
                'requests' => $requests,
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $db->beginTransaction();

        $stmt = $db->prepare("
            INSERT INTO maintenance_requests (
                student_id,
                room_id,
                title,
                description,
                category,
                priority,
                evidence_image,
                status,
                request_date,
                processed_by,
                processed_at,
                resolution_note
            )
            VALUES (
                :student_id,
                :room_id,
                :title,
                :description,
                :category,
                :priority,
                :evidence_image,
                'pending',
                NOW(),
                NULL,
                NULL,
                NULL
            )
        ");

        $stmt->execute([
            'student_id' => $student['id'],
            'room_id' => $contract['room_id'],
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'priority' => $priority,
            'evidence_image' => $evidenceImage
        ]);

        $requestId = $db->lastInsertId();

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
                'create',
                'maintenance_requests',
                :record_id,
                NULL,
                :new_value,
                :ip_address,
                :user_agent
            )
        ");

        $stmt->execute([
            'user_id' => $user['id'],
            'record_id' => $requestId,
            'new_value' => 'Student created maintenance request: ' . $title,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);

        $db->commit();

        redirectTo('student/maintenance');
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        echo '<h2>Gửi yêu cầu sửa chữa thất bại</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<a href="' . BASE_URL . '/index.php?route=student/maintenance">Quay lại</a>';
        exit;
    }
};

$routes['student/violations'] = function (PDO $db): void {
    Auth::requireRole('Student');

    $user = Auth::user();

    $stmt = $db->prepare("
        SELECT *
        FROM students
        WHERE user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user['id']
    ]);

    $student = $stmt->fetch();

    $violations = [];
    $totalPoints = 0;

    if ($student) {
        $stmt = $db->prepare("
            SELECT
                vr.id,
                vr.violation_type,
                vr.description,
                vr.penalty_points,
                vr.violation_date,
                vr.created_at,
                creator.username AS created_by_username
            FROM violation_records vr
            LEFT JOIN users creator ON creator.id = vr.recorded_by
            WHERE vr.student_id = :student_id
            ORDER BY 
                vr.violation_date DESC,
                vr.created_at DESC,
                vr.id DESC
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        $violations = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT COALESCE(SUM(penalty_points), 0)
            FROM violation_records
            WHERE student_id = :student_id
        ");

        $stmt->execute([
            'student_id' => $student['id']
        ]);

        $totalPoints = (int) $stmt->fetchColumn();
    }

    render('student/violations', [
        'title' => 'Vi phạm của tôi',
        'student' => $student,
        'violations' => $violations,
        'totalPoints' => $totalPoints
    ]);
};
