<?php

/**
 * BÀI TẬP IN-CLASS TASK - HỆ THỐNG QUẢN LÝ KTX
 * Phân hệ: Người dùng & Hồ sơ Sinh viên (roles, users, students)
 * CRUD đầy đủ: Create - Read - Update - Delete
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

// Lấy thông báo sau khi redirect để tránh submit lại form khi F5
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirectHome()
{
    header('Location: index.php');
    exit;
}

// ==========================================
// 2. BACKEND BUSINESS LOGIC & CRUD
// ==========================================

// Xử lý XÓA (DELETE) - Nhận yêu cầu từ URL
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];

    try {
        // Business Rule: Không cho phép xóa tài khoản Admin
        $stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception("Không tìm thấy tài khoản cần xóa!");
        }

        if ((int)$user['role_id'] === 1) {
            throw new Exception("Business Rule: Không được phép xóa tài khoản Admin khỏi hệ thống!");
        }

        // Do bảng students có ON DELETE CASCADE, xóa user sẽ tự động xóa student
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['message'] = "Đã xóa tài khoản và hồ sơ liên quan thành công!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi khi xóa: " . $e->getMessage();
    }

    redirectHome();
}

// Xử lý THÊM MỚI (CREATE) và CẬP NHẬT (UPDATE) - Nhận data từ Form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    $username = trim($_POST['username'] ?? '');
    $student_code = trim($_POST['student_code'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $gender = $_POST['gender'] ?? 'Nam';
    $dob = $_POST['dob'] ?? '';
    $faculty = trim($_POST['faculty'] ?? '');

    try {
        // Backend validation: không chỉ kiểm tra ở giao diện
        if ($username === '' || $student_code === '' || $full_name === '' || $dob === '' || $faculty === '') {
            throw new Exception("Vui lòng nhập đầy đủ các trường bắt buộc!");
        }

        if (!in_array($gender, ['Nam', 'Nữ'], true)) {
            throw new Exception("Giới tính không hợp lệ!");
        }

        if ($action === 'create') {
            // --- BUSINESS RULE 1: Kiểm tra trùng lặp Username ---
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                throw new Exception("Business Rule: Tên đăng nhập (Username) đã tồn tại!");
            }

            // --- BUSINESS RULE 2: Kiểm tra trùng lặp Mã SV ---
            $stmt = $pdo->prepare("SELECT id FROM students WHERE student_code = ?");
            $stmt->execute([$student_code]);
            if ($stmt->fetch()) {
                throw new Exception("Business Rule: Mã sinh viên đã tồn tại trong hệ thống!");
            }

            // Bắt đầu Transaction để đảm bảo User và Student phải cùng thành công
            $pdo->beginTransaction();

            // Mật khẩu mặc định khi tạo mới
            $passwordHash = password_hash('123456', PASSWORD_DEFAULT);

            // 1. Thêm vào bảng users (Vai trò 3 là Student)
            $stmtUser = $pdo->prepare("INSERT INTO users (username, password, role_id, status) VALUES (?, ?, 3, 'Hoạt động')");
            $stmtUser->execute([$username, $passwordHash]);
            $newUserId = $pdo->lastInsertId();

            // 2. Thêm vào bảng students
            $stmtStudent = $pdo->prepare("INSERT INTO students (user_id, student_code, full_name, gender, dob, faculty) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtStudent->execute([$newUserId, $student_code, $full_name, $gender, $dob, $faculty]);

            $pdo->commit();
            $_SESSION['message'] = "Thêm mới Sinh viên thành công! (Mật khẩu mặc định: 123456)";
            redirectHome();
        }

        if ($action === 'update') {
            $userId = (int)($_POST['user_id'] ?? 0);

            if ($userId <= 0) {
                throw new Exception("ID tài khoản không hợp lệ!");
            }

            // Lấy user cần sửa và kiểm tra đúng module Student
            $stmt = $pdo->prepare("SELECT u.id, u.role_id, s.id AS student_id
                                   FROM users u
                                   LEFT JOIN students s ON u.id = s.user_id
                                   WHERE u.id = ?");
            $stmt->execute([$userId]);
            $currentUser = $stmt->fetch();

            if (!$currentUser) {
                throw new Exception("Không tìm thấy tài khoản cần cập nhật!");
            }

            // Business Rule: Form này chỉ cập nhật hồ sơ sinh viên, không sửa Admin/Manager
            if ((int)$currentUser['role_id'] !== 3 || empty($currentUser['student_id'])) {
                throw new Exception("Business Rule: Chỉ được cập nhật tài khoản thuộc vai trò Student trong module này!");
            }

            // Business Rule: Username không được trùng với user khác
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
            $stmt->execute([$username, $userId]);
            if ($stmt->fetch()) {
                throw new Exception("Business Rule: Tên đăng nhập (Username) đã tồn tại ở tài khoản khác!");
            }

            // Business Rule: Mã sinh viên không được trùng với sinh viên khác
            $stmt = $pdo->prepare("SELECT id FROM students WHERE student_code = ? AND user_id <> ?");
            $stmt->execute([$student_code, $userId]);
            if ($stmt->fetch()) {
                throw new Exception("Business Rule: Mã sinh viên đã tồn tại ở hồ sơ khác!");
            }

            $pdo->beginTransaction();

            // 1. Cập nhật bảng users
            $stmtUser = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmtUser->execute([$username, $userId]);

            // 2. Cập nhật bảng students
            $stmtStudent = $pdo->prepare("UPDATE students
                                          SET student_code = ?, full_name = ?, gender = ?, dob = ?, faculty = ?
                                          WHERE user_id = ?");
            $stmtStudent->execute([$student_code, $full_name, $gender, $dob, $faculty, $userId]);

            $pdo->commit();
            $_SESSION['message'] = "Cập nhật hồ sơ sinh viên thành công!";
            redirectHome();
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = $e->getMessage();
        redirectHome();
    }
}

// Lấy dữ liệu để hiển thị form EDIT khi bấm nút Sửa
$editData = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $editUserId = (int)$_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT u.id AS user_id, u.username, u.status, u.role_id, r.role_name,
                                      s.student_code, s.full_name, s.gender, s.dob, s.faculty
                               FROM users u
                               JOIN roles r ON u.role_id = r.id
                               LEFT JOIN students s ON u.id = s.user_id
                               WHERE u.id = ?");
        $stmt->execute([$editUserId]);
        $editData = $stmt->fetch();

        if (!$editData) {
            $error = "Không tìm thấy dữ liệu cần sửa!";
        } elseif ((int)$editData['role_id'] !== 3 || empty($editData['student_code'])) {
            // Business Rule: không cho sửa Admin/Manager trong module sinh viên
            $editData = null;
            $error = "Business Rule: Chỉ được sửa hồ sơ Sinh viên, không sửa Admin/Manager ở form này!";
        }
    } catch (Exception $e) {
        $error = "Lỗi khi lấy dữ liệu sửa: " . $e->getMessage();
    }
}

// Lấy danh sách (READ) - Dùng JOIN để lấy dữ liệu từ 3 bảng
$query = "SELECT u.id as user_id, u.username, u.status, u.role_id, r.role_name,
                 s.student_code, s.full_name, s.gender, s.dob, s.faculty
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
            <p class="text-sm text-green-700 font-semibold mt-2">CRUD đầy đủ: Create - Read - Update - Delete + Backend Business Rules</p>
        </header>

        <!-- Hiển thị thông báo -->
        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded">
                <p class="font-bold">Thành công</p>
                <p><?= e($message) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded">
                <p class="font-bold">Lỗi Nghiệp vụ (Business Rule)</p>
                <p><?= e($error) ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- CỘT TRÁI: FORM THÊM MỚI / CẬP NHẬT -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 h-fit">
                <?php if ($editData): ?>
                    <h2 class="text-xl font-semibold mb-4 text-orange-700 border-b pb-2">Cập Nhật Hồ Sơ Sinh Viên</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="user_id" value="<?= e($editData['user_id']) ?>">
                    <?php else: ?>
                        <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Thêm Mới Sinh Viên</h2>
                        <form method="POST" action="index.php">
                            <input type="hidden" name="action" value="create">
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập (Username) *</label>
                            <input type="text" name="username" required value="<?= e($editData['username'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã Sinh Viên *</label>
                            <input type="text" name="student_code" required value="<?= e($editData['student_code'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và Tên *</label>
                            <input type="text" name="full_name" required value="<?= e($editData['full_name'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                                <select name="gender" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Nam" <?= (($editData['gender'] ?? '') === 'Nam') ? 'selected' : '' ?>>Nam</option>
                                    <option value="Nữ" <?= (($editData['gender'] ?? '') === 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh *</label>
                                <input type="date" name="dob" required value="<?= e($editData['dob'] ?? '') ?>"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khoa/Viện *</label>
                            <input type="text" name="faculty" required value="<?= e($editData['faculty'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <?php if ($editData): ?>
                            <button type="submit" class="w-full bg-orange-600 text-white font-semibold py-2 px-4 rounded hover:bg-orange-700 transition duration-200">
                                Cập Nhật Hồ Sơ
                            </button>
                            <a href="index.php" class="block text-center mt-3 text-gray-600 hover:text-gray-900 text-sm">Hủy sửa / Quay lại thêm mới</a>
                        <?php else: ?>
                            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
                                + Tạo Hồ Sơ Sinh Viên
                            </button>
                        <?php endif; ?>
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
                                <tr class="hover:bg-gray-50 border-b border-gray-100 <?= ($editData && (int)$editData['user_id'] === (int)$row['user_id']) ? 'bg-orange-50' : '' ?>">
                                    <td class="py-3 px-4"><?= e($row['user_id']) ?></td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-gray-800"><?= e($row['username']) ?></div>
                                        <div class="text-xs inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded mt-1">
                                            <?= e($row['role_name']) ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if ($row['full_name']): ?>
                                            <div class="font-medium"><?= e($row['full_name']) ?></div>
                                            <div class="text-gray-500 text-xs"><?= e($row['student_code']) ?> - <?= e($row['gender']) ?></div>
                                            <div class="text-gray-400 text-xs">Ngày sinh: <?= e($row['dob']) ?></div>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">Không có hồ sơ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4"><?= e($row['faculty'] ?? '-') ?></td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <?php if ((int)$row['role_id'] === 3 && !empty($row['student_code'])): ?>
                                            <a href="?action=edit&id=<?= e($row['user_id']) ?>"
                                                class="text-orange-600 hover:text-orange-800 font-medium text-sm mr-3">
                                                Sửa
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-300 text-sm mr-3">Sửa</span>
                                        <?php endif; ?>

                                        <a href="?action=delete&id=<?= e($row['user_id']) ?>"
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

                <div class="mt-4 text-sm text-gray-600 bg-gray-50 p-3 rounded border">
                    <p><strong>Mapping ERD:</strong> module này dùng 3 bảng liên quan: <code>roles</code>, <code>users</code>, <code>students</code>.</p>
                    <p><strong>Quan hệ:</strong> <code>users.role_id → roles.id</code> và <code>students.user_id → users.id</code>.</p>
                </div>
            </div>

        </div>
    </div>
</body>

</html>