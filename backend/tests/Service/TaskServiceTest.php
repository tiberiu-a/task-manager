<?php

namespace Tests\Service;

use App\Repository\TaskRepository;
use App\Service\TaskService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    public function testCreateTaskRejectsEmptyTitle(): void
    {
        $repository = $this->createMock(TaskRepository::class);
        $service = new TaskService($repository);

        $this->expectException(InvalidArgumentException::class);

        $service->createTask(['creator_id' => 5, 'title' => 'Task test']);
    }
}