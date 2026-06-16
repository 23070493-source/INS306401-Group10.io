<?php

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
        'title' => 'Student Dashboard',
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
        'title' => 'Available Rooms',
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

    if (!$student) {
        render('student/register_room', [
            'title' => 'Register Room',
            'student' => null,
            'buildings' => [],
            'semesters' => [],
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
            FROM room_registrations
            WHERE student_id = :student_id
              AND semester_id = :semester_id
        ");
        $stmt->execute([
            'student_id' => $student['id'],
            'semester_id' => $semesterId
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Bạn đã có đơn đăng ký trong học kỳ này.';
        }

        if (!empty($errors)) {
            render('student/register_room', [
                'title' => 'Register Room',
                'student' => $student,
                'buildings' => $buildings,
                'semesters' => $semesters,
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

        render('student/register_room', [
            'title' => 'Register Room',
            'student' => $student,
            'buildings' => $buildings,
            'semesters' => $semesters,
            'errors' => [],
            'success' => 'Gửi đơn đăng ký phòng thành công. Trạng thái hiện tại: pending.',
            'old' => []
        ]);
        return;
    }

    render('student/register_room', [
        'title' => 'Register Room',
        'student' => $student,
        'buildings' => $buildings,
        'semesters' => $semesters,
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
        'title' => 'My Registration',
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
        'title' => 'My Contract',
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
        'title' => 'My Invoices',
        'student' => $student,
        'invoices' => $invoices
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
        'title' => 'Submit Bank Transfer',
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
    $paymentDate = trim($_POST['payment_date'] ?? '');
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

    if ($paymentDate === '') {
        $errors[] = 'Vui lòng nhập thời gian chuyển khoản.';
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
                'title' => 'Submit Bank Transfer',
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

        $paymentDateSql = str_replace('T', ' ', $paymentDate);

        if (strlen($paymentDateSql) === 16) {
            $paymentDateSql .= ':00';
        }

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
        'title' => 'Maintenance Request',
        'student' => $student,
        'contract' => $contract,
        'requests' => $requests,
        'errors' => [],
        'old' => []
    ]);
};

$routes['student/maintenance-store'] = function (PDO $db): void {
    Auth::requireRole('Student');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectTo('student/maintenance');
    }

    $user = Auth::user();

    $category = trim($_POST['category'] ?? '');
    $priority = trim($_POST['priority'] ?? 'medium');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $errors = [];
    $evidenceImagePath = null;

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

    if (isset($_FILES['evidence_image']) && $_FILES['evidence_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['evidence_image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload ảnh minh chứng thất bại.';
        } else {
            $maxSize = 5 * 1024 * 1024;

            if ($_FILES['evidence_image']['size'] > $maxSize) {
                $errors[] = 'Ảnh minh chứng không được vượt quá 5MB.';
            }

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['evidence_image']['tmp_name']);
            finfo_close($finfo);

            if (!array_key_exists($mimeType, $allowedMimeTypes)) {
                $errors[] = 'Ảnh minh chứng chỉ được dùng định dạng JPG, PNG hoặc WEBP.';
            }

            if (empty($errors)) {
                $uploadDir = dirname(__DIR__) . '/public/uploads/maintenance';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = $allowedMimeTypes[$mimeType];
                $fileName = 'maintenance_' . date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                $targetPath = $uploadDir . '/' . $fileName;

                if (!move_uploaded_file($_FILES['evidence_image']['tmp_name'], $targetPath)) {
                    $errors[] = 'Không thể lưu ảnh minh chứng.';
                } else {
                    $evidenceImagePath = 'uploads/maintenance/' . $fileName;
                }
            }
        }
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
                'title' => 'Maintenance Request',
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
                status,
                request_date,
                processed_by,
                processed_at,
                resolution_note,
                evidence_image
            )
            VALUES (
                :student_id,
                :room_id,
                :title,
                :description,
                :category,
                :priority,
                'pending',
                NOW(),
                NULL,
                NULL,
                NULL,
                :evidence_image
            )
        ");

        $stmt->execute([
            'student_id' => $student['id'],
            'room_id' => $contract['room_id'],
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'priority' => $priority,
            'evidence_image' => $evidenceImagePath
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