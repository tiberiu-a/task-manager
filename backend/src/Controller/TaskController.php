<?php

namespace App\Controller;

use App\Exception\TaskNotFoundException;
use App\Http\JsonBodyReader;
use App\Service\TaskService;
use InvalidArgumentException;

class TaskController
{
    private TaskService $task_service;
    private JsonBodyReader $json_body_reader;

    public function __construct(TaskService $task_service, JsonBodyReader $json_body_reader)
    {
        $this->task_service = $task_service;
        $this->json_body_reader = $json_body_reader;
    }

    public function create(): void
    {
        $json = file_get_contents('php://input');
        $user_id = $_SESSION['user_id'];

        try {
            $data = $this->json_body_reader->decodeJson($json);
            $id = $this->task_service->createTask($user_id, $data);
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
        $tasks = $this->task_service->listTasks($user_id);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }

    public function show(int $task_id): void
    {
        $user_id = $_SESSION['user_id'];
        
        try {
            $task = $this->task_service->getTask($task_id, $user_id);
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
        $user_id = $_SESSION['user_id'];

        try {
            $data = $this->json_body_reader->decodeJson($json);
            $this->task_service->updateTask($task_id, $user_id, $data);            
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
            $this->task_service->deleteTask($task_id, $user_id);
            http_response_code(204);
        } catch (TaskNotFoundException $e) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => "The task with id $task_id does not exist"]);
        }
    }
}
