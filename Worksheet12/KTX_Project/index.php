<?php

/**
 * BÀI TẬP IN-CLASS TASK - HỆ THỐNG QUẢN LÝ KTX
 * Phân hệ: Người dùng & Hồ sơ Sinh viên (roles, users, students)
 */

session_start();

// ==========================================
// 1. DATABASE CONNECTION (SINGLETON PATTERN)
// ==========================================
class DB
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $host = 'localhost';
        $db   = 'ktx_db'; // Tên DB trong file SQL
        $user = 'root';   // User mặc định của XAMPP
        $pass = '';       // Pass mặc định của XAMPP

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("Lỗi kết nối CSDL: Vui lòng kiểm tra XAMPP và Import file ktx_db.sql. Chi tiết: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance->pdo;
    }
}

$pdo = DB::getInstance();
$message = '';
$error = '';

// ==========================================
// 2. BACKEND BUSINESS LOGIC & CRUD
// ==========================================

// Xử lý XÓA (DELETE) - Nhận yêu cầu từ URL
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $userId = $_GET['id'];
    try {
        // Business Rule: Không cho phép xóa tài khoản Admin
        $stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && $user['role_id'] == 1) {
            $error = "Business Rule: Không được phép xóa tài khoản Admin khỏi hệ thống!";
        } else {
            // Do bảng students có cờ ON DELETE CASCADE, xóa user sẽ tự động xóa student
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $message = "Đã xóa tài khoản và hồ sơ liên quan thành công!";
        }
    } catch (Exception $e) {
        $error = "Lỗi khi xóa: " . $e->getMessage();
    }
}

// Xử lý THÊM MỚI (CREATE) - Nhận data từ Form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $username = trim($_POST['username']);
    $student_code = trim($_POST['student_code']);
    $full_name = trim($_POST['full_name']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $faculty = $_POST['faculty'];

    // Mật khẩu mặc định khi tạo mới
    $passwordHash = password_hash('123456', PASSWORD_DEFAULT);

    try {
        // --- BUSINESS RULE 1: Kiểm tra trùng lặp Username ---
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception("Tên đăng nhập (Username) đã tồn tại!");
        }

        // --- BUSINESS RULE 2: Kiểm tra trùng lặp Mã SV ---
        $stmt = $pdo->prepare("SELECT id FROM students WHERE student_code = ?");
        $stmt->execute([$student_code]);
        if ($stmt->fetch()) {
            throw new Exception("Mã sinh viên đã tồn tại trong hệ thống!");
        }

        // Bắt đầu Transaction để đảm bảo tính toàn vẹn dữ liệu (Thêm User và Student phải cùng thành công)
        $pdo->beginTransaction();

        // 1. Thêm vào bảng users (Vai trò 3 là Student)
        $stmtUser = $pdo->prepare("INSERT INTO users (username, password, role_id, status) VALUES (?, ?, 3, 'Hoạt động')");
        $stmtUser->execute([$username, $passwordHash]);
        $newUserId = $pdo->lastInsertId(); // Lấy ID vừa tạo

        // 2. Thêm vào bảng students
        $stmtStudent = $pdo->prepare("INSERT INTO students (user_id, student_code, full_name, gender, dob, faculty) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtStudent->execute([$newUserId, $student_code, $full_name, $gender, $dob, $faculty]);

        // Hoàn tất Transaction
        $pdo->commit();
        $message = "Thêm mới Sinh viên thành công! (Mật khẩu mặc định: 123456)";
    } catch (Exception $e) {
        // Nếu có lỗi ở bất kỳ bước nào, rollback lại toàn bộ
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

// Lấy danh sách (READ) - Dùng lệnh JOIN để lấy dữ liệu từ 3 bảng
$query = "SELECT u.id as user_id, u.username, u.status, r.role_name, 
                 s.student_code, s.full_name, s.gender, s.faculty
          FROM users u
          JOIN roles r ON u.role_id = r.id
          LEFT JOIN students s ON u.id = s.user_id
          ORDER BY u.id DESC";
$stmt = $pdo->query($query);
$allUsers = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hồ sơ Sinh viên KTX</title>
    <!-- Sử dụng Tailwind CSS cho giao diện sạch đẹp theo yêu cầu -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans">

    <div class="max-w-7xl mx-auto p-6">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-blue-700">HỆ THỐNG QUẢN LÝ KÝ TÚC XÁ</h1>
            <p class="text-gray-600">Phân hệ 1: Quản lý Người dùng & Hồ sơ Sinh viên (roles, users, students)</p>
        </header>

        <!-- Hiển thị thông báo -->
        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                <p class="font-bold">Thành công</p>
                <p><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
                <p class="font-bold">Lỗi Nghiệp vụ (Business Rule)</p>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- CỘT TRÁI: FORM THÊM MỚI -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 h-fit">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Thêm Mới Sinh Viên</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="create">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập (Username) *</label>
                        <input type="text" name="username" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã Sinh Viên *</label>
                        <input type="text" name="student_code" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và Tên *</label>
                        <input type="text" name="full_name" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                            <select name="gender" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh *</label>
                            <input type="date" name="dob" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Khoa/Viện *</label>
                        <input type="text" name="faculty" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
                        + Tạo Hồ Sơ Sinh Viên
                    </button>
                </form>
            </div>

            <!-- CỘT PHẢI: BẢNG DANH SÁCH -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Danh sách Tài khoản & Hồ sơ</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-3 px-4 font-semibold border-b">ID</th>
                                <th class="py-3 px-4 font-semibold border-b">Tài khoản (Role)</th>
                                <th class="py-3 px-4 font-semibold border-b">Thông tin Sinh viên</th>
                                <th class="py-3 px-4 font-semibold border-b">Khoa</th>
                                <th class="py-3 px-4 font-semibold border-b text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $row): ?>
                                <tr class="hover:bg-gray-50 border-b border-gray-100">
                                    <td class="py-3 px-4"><?= $row['user_id'] ?></td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($row['username']) ?></div>
                                        <div class="text-xs inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded mt-1">
                                            <?= htmlspecialchars($row['role_name']) ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if ($row['full_name']): ?>
                                            <div class="font-medium"><?= htmlspecialchars($row['full_name']) ?></div>
                                            <div class="text-gray-500 text-xs"><?= htmlspecialchars($row['student_code']) ?> - <?= htmlspecialchars($row['gender']) ?></div>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">Không có hồ sơ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($row['faculty'] ?? '-') ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="?action=delete&id=<?= $row['user_id'] ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này và mọi hồ sơ liên quan?');"
                                            class="text-red-500 hover:text-red-700 font-medium text-sm">
                                            Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</body>

</html>