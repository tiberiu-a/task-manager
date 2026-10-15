<?php

namespace App\Controller;

use App\Exception\TaskNotFoundException;
use App\Service\TaskService;
use InvalidArgumentException;

class TaskController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function create(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        try {
            $user_id = $_SESSION['user_id'];
            $id = $this->taskService->createTask($user_id, $data);
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode(['id' => $id]);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function list(): void
    {
        $user_id = $_SESSION['user_id'];
        $tasks = $this->taskService->listTasks($user_id);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }

    public function show(int $task_id): void
    {
        $user_id = $_SESSION['user_id'];
        
        try {
            $task = $this->taskService->getTask($task_id, $user_id);
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($task);
        } catch (TaskNotFoundException $e) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'The task with id ' . $task_id . ' does not exist']);
        }
    }

    public function update(int $task_id): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $user_id = $_SESSION['user_id'];

        try {
            $this->taskService->updateTask($task_id, $user_id, $data);            
            http_response_code(204);
        } catch (TaskNotFoundException $e) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete(int $task_id): void
    {
        $user_id = $_SESSION['user_id'];
        
        try {
            $this->taskService->deleteTask($task_id, $user_id);
            http_response_code(204);
        } catch (TaskNotFoundException $e) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => "The task with id $task_id does not exist"]);
        }
    }
}
