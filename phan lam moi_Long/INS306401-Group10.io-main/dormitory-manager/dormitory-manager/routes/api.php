<?php

$routes['api/rooms/available'] = function (PDO $db): void {
    Auth::requireLogin();

    $service = new RoomAllocationService($db);

    Response::json([
        'success' => true,
        'data' => $service->availableRooms()
    ]);
};

$routes['api/registrations/suggestions'] = function (PDO $db): void {
    Auth::requireRole('Manager');

    $registrationId = (int) ($_GET['id'] ?? 0);

    if ($registrationId <= 0) {
        Response::json([
            'success' => false,
            'message' => 'Mã đơn đăng ký không hợp lệ.'
        ], 422);
        return;
    }

    $service = new RoomAllocationService($db);

    Response::json([
        'success' => true,
        'data' => $service->suggestForRegistration($registrationId)
    ]);
};

$routes['api/dashboard/summary'] = function (PDO $db): void {
    Auth::requireLogin();

    $user = Auth::user();
    $roleName = $user['role_name'] ?? '';

    if ($roleName === 'Admin') {
        Response::json([
            'success' => true,
            'role' => 'Admin',
            'data' => adminDashboardSummary($db)
        ]);
        return;
    }

    if ($roleName === 'Manager') {
        Response::json([
            'success' => true,
            'role' => 'Manager',
            'data' => [
                'pending_registrations' => $db->query("SELECT COUNT(*) FROM room_registrations WHERE status = 'pending'")->fetchColumn(),
                'active_contracts' => $db->query("SELECT COUNT(*) FROM contracts WHERE status = 'active'")->fetchColumn(),
                'unpaid_invoices' => $db->query("SELECT COUNT(*) FROM invoices WHERE status IN ('unpaid', 'overdue', 'partially_paid')")->fetchColumn(),
                'open_maintenance' => $db->query("SELECT COUNT(*) FROM maintenance_requests WHERE status IN ('pending', 'in_progress')")->fetchColumn()
            ]
        ]);
        return;
    }

    Response::json([
        'success' => true,
        'role' => 'Student',
        'data' => [
            'message' => 'Student dashboard uses private student data from the web route.'
        ]
    ]);
};
