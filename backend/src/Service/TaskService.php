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
        $this->assertHasAValidTitle($data);

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

        $this->assertHasAValidTitle($data);

        $this->taskRepository->update($task_id, $user_id,  $data);
    }

    public function deleteTask(int $task_id, int $user_id): void
    {
        if ($this->taskRepository->delete($task_id, $user_id) === false) {
            throw new TaskNotFoundException("The task with id $task_id does not exist");
        }
    }

    private function assertHasAValidTitle(array $data): void
    {
        if (!isset($data['title'])) {
            throw new InvalidArgumentException("You can't have a task with an empty title");
        }

        if (gettype($data['title']) !== 'string')  {
            throw new InvalidArgumentException("Task title is of the wrong type");
        }

        if (trim($data['title']) === "") {
            throw new InvalidArgumentException("You can't have a task with an empty title");
        }

        // The limit is based on the maximum value in the database that a VARCHAR can hold
        $max_char_title = 255;

        if (mb_strlen($data['title']) > $max_char_title) {
            throw new InvalidArgumentException("The task title can't be more than {$max_char_title} characters long");
        }
    }    
}
