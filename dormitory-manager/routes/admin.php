<?php

$routes['admin/dashboard'] = function (PDO $db): void {
    Auth::requireRole('Admin');

    $summary = [
        'total_users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'total_students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
        'total_buildings' => $db->query("SELECT COUNT(*) FROM buildings")->fetchColumn(),
        'total_rooms' => $db->query("SELECT COUNT(*) FROM rooms")->fetchColumn(),
        'available_rooms' => $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'available'")->fetchColumn(),
        'maintenance_rooms' => $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'maintenance'")->fetchColumn(),
    ];

    render('admin/dashboard', [
        'title' => 'Admin Dashboard',
        'summary' => $summary
    ]);
};