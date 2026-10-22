<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Controller\TaskController;
use App\Controller\UserController;
use App\Http\JsonBodyReader;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\TaskService;
use App\Service\UserService;

// Do not accept sessions that do not originate from the server
ini_set('session.use_strict_mode', 1);
// "Secure" will be set to "true" when we deploy it to the server
session_set_cookie_params([
    'httponly' => true,
    'path' => "/",
    'lifetime' => 0,
    'secure' => false,
    'samesite' => 'Lax'
]);

$front_url = getenv('FRONT_URL');

if (!empty($front_url)) {
    header("Access-Control-Allow-Origin: {$front_url}");
} else {
    error_log('FRONT_URL is missing from the .env file or the value is an empty string');
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 7200');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(204);
    exit();
}

function exception_handler(Throwable $throwable): void
{
    error_log((string) $throwable);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Server error']);
}

set_exception_handler('exception_handler');

$db = new Database();
$pdo = $db->getConnection();
$matches = array();
$json_body_reader = new JsonBodyReader();

$taskRepository = new TaskRepository($pdo);
$taskService = new TaskService($taskRepository);
$taskController = new TaskController($taskService, $json_body_reader);

$userRepository = new UserRepository($pdo);
$userService = new UserService($userRepository);
$userController = new UserController($userService, $json_body_reader);



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/register') {

    $userController->create();
    exit();

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/login') {

    $userController->login();
    exit();

}

session_start();

if (array_key_exists('user_id', $_SESSION) === false) {

    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'You are not logged in']);
    exit();

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/logout') {

    $userController->logout();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/tasks') {

    $taskController->create();

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/tasks') {

    $taskController->list();

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('#^/tasks/(\d+)$#', $_SERVER['REQUEST_URI'], $matches)) {

    $id = (int) $matches[1];
    $taskController->show($id);

} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && preg_match('#^/tasks/(\d+)$#', $_SERVER['REQUEST_URI'], $matches)) {

    $id = (int) $matches[1];
    $taskController->update($id);

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && preg_match('#^/tasks/(\d+)$#', $_SERVER['REQUEST_URI'], $matches)) {

    $id = (int) $matches[1];
    $taskController->delete($id);

} else {

    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'The route was not found']);

}
