<?php

require_once __DIR__ . '/Database.php';

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function attempt(string $username, string $password): bool
    {
        $username = trim($username);
        $password = trim($password);

        self::start();

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT 
                u.id,
                u.username,
                u.password_hash,
                u.email,
                u.phone,
                u.avatar,
                u.status,
                r.role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.username = :username
              AND u.status = 'active'
            LIMIT 1
        ");

        $stmt->execute([
            'username' => $username
        ]);

        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        unset($user['password_hash']);

        $_SESSION['user'] = $user;

        return true;
    }

    public static function check(): bool
    {
        self::start();

        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        self::start();

        return $_SESSION['user'] ?? null;
    }

    public static function refreshUserSession(): void
    {
        self::start();

        if (!isset($_SESSION['user']['id'])) {
            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT 
                u.id,
                u.username,
                u.email,
                u.phone,
                u.avatar,
                u.status,
                r.role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $_SESSION['user']['id']
        ]);

        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = $user;
        }
    }

    public static function logout(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/index.php?route=login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();

        $user = self::user();

        if (!$user || $user['role_name'] !== $role) {
            http_response_code(403);
            echo '403 Forbidden - You do not have permission to access this page.';
            exit;
        }
    }

    public static function dashboardRoute(): string
    {
        $user = self::user();

        if (!$user) {
            return 'login';
        }

        if ($user['role_name'] === 'Admin') {
            return 'admin/dashboard';
        }

        if ($user['role_name'] === 'Manager') {
            return 'manager/dashboard';
        }

        if ($user['role_name'] === 'Student') {
            return 'student/dashboard';
        }

        return 'login';
    }
}