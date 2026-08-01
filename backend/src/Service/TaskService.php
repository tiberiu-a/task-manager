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

        $this->assertValidDateRange($data);

        return $this->taskRepository->create($data);
    }

    public function listTasks():array
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

        $this->assertHasTitle($data);
        
        $this->assertValidDateRange($data);

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

    private function assertValidDateRange(array $data): void
    {
        if (!empty($data['due_date']) && !empty($data['start_date'])) {
            $start_date = new DateTime($data['start_date']);
            $due_date = new DateTime($data['due_date']);
            if ($start_date >= $due_date) {
                throw new InvalidArgumentException("Start date can't be bigger or equal to due date");
            }
        }
    }
}
