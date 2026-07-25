<?php

namespace App\Service;

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

    private function assertHasTitle(array $data): void 
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException("createTask: you can't have a task with an empty title");
        }
    }

    private function assertHasCreatorId(array $data): void
    {
        if (empty($data['creator_id'])) {
            throw new InvalidArgumentException("createTask: you can't have a task without a creator_id");
        }
    }

    private function assertValidDateRange(array $data): void
    {
        if (!empty($data['due_date']) && !empty($data['start_date'])) {
            $start_date = new DateTime($data['start_date']);
            $due_date = new DateTime($data['due_date']);
            if ($start_date >= $due_date) {
                throw new InvalidArgumentException("createTask: start_date can't be bigger of equal to due_date");
            }
        }
    }
}
