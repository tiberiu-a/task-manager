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
            $id = $this->taskService->createTask($data);
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
        $tasks = $this->taskService->listTasks();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }

    public function show(int $id): void
    {
        $task = $this->taskService->getTask($id);

        if ($task !== null) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($task);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'The task with id ' . $id . ' does not exist']);
        }
    }

    public function update(int $id): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        try {
            $this->taskService->updateTask($id, $data);            
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

    public function delete(int $id): void
    {
        $isTaskDeleted = $this->taskService->deleteTask($id);

        if ($isTaskDeleted) {
            http_response_code(204);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => "The task with id $id does not exist"]);
        }
    }
}
