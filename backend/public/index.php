<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Controller\TaskController;
use App\Controller\UserController;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\TaskService;
use App\Service\UserService;

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 7200');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(204);
    exit();

}

$db = new Database();
$pdo = $db->getConnection();
$matches = array();

$taskRepository = new TaskRepository($pdo);
$taskService = new TaskService($taskRepository);
$taskController = new TaskController($taskService);

$userRepository = new UserRepository($pdo);
$userService = new UserService($userRepository);
$userController = new UserController($userService);

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

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/register') {

    $userController->create();

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/login') {

    $userController->login();

} else {

    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'The route was not found']);

}