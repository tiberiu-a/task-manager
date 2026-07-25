<?php

namespace App\Controller;

use App\Service\TaskService;
use InvalidArgumentException;

class TaskController
{   
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function create():void
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
}