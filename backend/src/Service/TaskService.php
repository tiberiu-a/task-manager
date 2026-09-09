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

    public function createTask(array $data): int
    {
        $this->assertHasTitle($data);

        $this->assertHasCreatorId($data);

        $start_date = $this->validateDate($data['start_date'] ?? null);

        $due_date = $this->validateDate($data['due_date'] ?? null);

        $this->assertValidDateRange($start_date, $due_date);

        return $this->taskRepository->create($data);
    }

    public function listTasks(): array
    {
        return $this->taskRepository->findAll();
    }

    public function getTask(int $id): ?array
    {
        return $this->taskRepository->findById($id);
    }

    public function updateTask(int $id, array $data): bool
    {

        if ($this->taskRepository->findById($id) === null) {
            throw new TaskNotFoundException("The task with id $id does not exist");
        }

        $start_date = $this->validateDate($data['start_date'] ?? null);

        $due_date = $this->validateDate($data['due_date'] ?? null);

        $this->assertValidDateRange($start_date, $due_date);

        $this->assertHasTitle($data);

        return $this->taskRepository->update($id, $data);
    }

    public function deleteTask(int $id): bool
    {
        return $this->taskRepository->delete($id);
    }

    private function assertHasTitle(array $data): void
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException("You can't have a task with an empty title");
        }
    }

    private function assertHasCreatorId(array $data): void
    {
        if (empty($data['creator_id'])) {
            throw new InvalidArgumentException("You can't have a task without a creator_id");
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
