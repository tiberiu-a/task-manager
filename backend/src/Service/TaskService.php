<?php

namespace App\Service;

use App\Exception\TaskNotFoundException;
use App\Repository\TaskRepository;
use InvalidArgumentException;

class TaskService
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function createTask(int $user_id, array $data): int
    {
        $this->assertHasTitle($data);

        return $this->taskRepository->create($user_id, $data);
    }

    public function listTasks(int $user_id): array
    {
        return $this->taskRepository->findAll($user_id);
    }

    public function getTask(int $task_id, int $user_id): array
    {
        $task = $this->taskRepository->findById($task_id, $user_id);

        if ($task === null) {
            throw new TaskNotFoundException("The task with id $task_id does not exist");
        }

        return $task;
    }

    public function updateTask(int $task_id, int $user_id, array $data): void
    {

        if ($this->taskRepository->findById($task_id, $user_id) === null) {
            throw new TaskNotFoundException("The task with id $task_id does not exist");
        }

        $this->assertHasTitle($data);

        $this->taskRepository->update($task_id, $user_id,  $data);
    }

    public function deleteTask(int $task_id, int $user_id): void
    {
        if ($this->taskRepository->delete($task_id, $user_id) === false) {
            throw new TaskNotFoundException("The task with id $task_id does not exist");
        }
    }

    private function assertHasTitle(array $data): void
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException("You can't have a task with an empty title");
        }
    }    
}
