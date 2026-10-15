<?php

namespace App\Service;

use App\Exception\TaskNotFoundException;
use App\Repository\TaskRepository;
use DateTime;
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

        $start_date = $this->validateDate($data['start_date'] ?? null);

        $due_date = $this->validateDate($data['due_date'] ?? null);

        $this->assertValidDateRange($start_date, $due_date);

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

        $start_date = $this->validateDate($data['start_date'] ?? null);

        $due_date = $this->validateDate($data['due_date'] ?? null);

        $this->assertValidDateRange($start_date, $due_date);

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

    private function validateDate(?string $date): ?DateTime
    {
        if (!$date) {
            return null;
        }
        $obj_date = DateTime::createFromFormat('Y-m-d', $date);

        if (!$obj_date) {
            throw new InvalidArgumentException("The date needs to have this format Y-m-d (ex. 2000-01-30)");
        }

        if ($obj_date->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException("The day or month is out of range");
        }

        return $obj_date;
    }

    private function assertValidDateRange(?DateTime $start_date, ?DateTime $due_date): void
    {
        if ($start_date !== null && $due_date !== null && $start_date >= $due_date) {
            throw new InvalidArgumentException("Start date can't be bigger or equal to due date");
        }
    }
}
