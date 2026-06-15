<?php

require_once __DIR__ . '/../app/Core/Database.php';

$db = Database::getConnection();

$stmt = $db->query("
    SELECT 
        u.id,
        u.username,
        u.email,
        r.role_name,
        u.status
    FROM users u
    JOIN roles r ON r.id = u.role_id
    ORDER BY u.id
");

$users = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dormitory Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f6f8;
            color: #222;
        }

        .container {
            background: white;
            padding: 24px;
            border-radius: 12px;
            max-width: 1000px;
            margin: auto;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        h1 {
            color: #1f4e79;
            margin-bottom: 8px;
        }

        .success {
            background: #e8f7ee;
            color: #176b35;
            padding: 12px;
            border-radius: 8px;
            margin: 16px 0;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background: #1f4e79;
            color: white;
        }

        tr:nth-child(even) {
            background: #f8f9fb;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Dormitory Manager</h1>
    <p>Hệ thống Quản lý KTX & Đăng ký Chỗ ở</p>

    <div class="success">
        Database connected successfully!
    </div>

    <h2>Users with Roles</h2>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role_name']) ?></td>
                <td><?= htmlspecialchars($user['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>