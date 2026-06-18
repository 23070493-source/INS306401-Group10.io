<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/Core/Response.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Services/RoomAllocationService.php';
require_once __DIR__ . '/../app/Services/BillingService.php';
require_once __DIR__ . '/../app/Services/ViolationService.php';

Auth::start();

$db = Database::getConnection();
$route = $_GET['route'] ?? 'home';

function redirectTo(string $route): void
{
    header('Location: ' . BASE_URL . '/index.php?route=' . $route);
    exit;
}

function render(string $view, array $data = []): void
{
    extract($data);

    require __DIR__ . '/../app/Views/layouts/header.php';
    require __DIR__ . '/../app/Views/' . $view . '.php';
    require __DIR__ . '/../app/Views/layouts/footer.php';
}

$routes = [];

require __DIR__ . '/../routes/auth.php';
require __DIR__ . '/../routes/api.php';
require __DIR__ . '/../routes/admin.php';
require __DIR__ . '/../routes/student.php';
require __DIR__ . '/../routes/manager.php';

if (isset($routes[$route])) {
    $routes[$route]($db);
    exit;
}

http_response_code(404);
echo '404 Not Found';
