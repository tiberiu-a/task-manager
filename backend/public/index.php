<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Controller\TaskController;
use App\Repository\TaskRepository;
use App\Service\TaskService;

$db = new Database();
$pdo = $db->getConnection();

$taskRepository = new TaskRepository($pdo);
$taskService = new TaskService($taskRepository);
$taskController = new TaskController($taskService);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/tasks') {
    $taskController->create();
} else {
    http_response_code(404);
    header('Content=Type: application/json');
    echo json_encode(['error' => 'The route was not found']);
}