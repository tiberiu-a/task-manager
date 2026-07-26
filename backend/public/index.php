<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Controller\TaskController;
use App\Repository\TaskRepository;
use App\Service\TaskService;

$db = new Database();
$pdo = $db->getConnection();
$matches = array();

$taskRepository = new TaskRepository($pdo);
$taskService = new TaskService($taskRepository);
$taskController = new TaskController($taskService);

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