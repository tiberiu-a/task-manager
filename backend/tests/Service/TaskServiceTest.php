<?php

namespace Tests\Service;

use App\Repository\TaskRepository;
use App\Service\TaskService;
use App\Exception\TaskNotFoundException;
use InvalidArgumentException;
use Override;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\Stub;

class TaskServiceTest extends TestCase
{
    private TaskRepository&Stub $repository;
    private TaskService $service;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = $this->createStub(TaskRepository::class);
        $this->service = new TaskService($this->repository);
    }

    public function testCreateTaskRejectsEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->createTask(['creator_id' => 5]);
    }

    public function testCreateTaskRejectsMissingCreatorId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->createTask(['title' => 'Test']);
    }

    public function testCreateTaskRejectsDueDateBeforeStartDate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->createTask([
            'title' => 'Test',
            'creator_id' => 5,
            'start_date' => '2026-08-10',
            'due_date' => '2026-08-09'
        ]);
    }

    public function testCreateTaskReturnsTheNewId(): void
    {
        $this->repository->method('create')->willReturn(42);

        $result = $this->service->createTask([
            'title' => 'Test',
            'creator_id' => 5,
        ]);

        $this->assertSame(42, $result);
    }

    public function testUpdateTaskThrowsWhenTaskDoesNotExist(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(TaskNotFoundException::class);

        $this->service->updateTask(100, []);
    }

    public function testUpdateTaskRejectsEmptyTitle(): void
    {
        $this->repository->method('findById')->willReturn(['id' => 5]);

        $this->expectException(InvalidArgumentException::class);

        $this->service->updateTask(5, []);
    }

    public function testUpdateTaskRejectsDueDateBeforeStartDate(): void
    {
        $this->repository->method('findById')->willReturn(['id' => 5]);

        $this->expectException(InvalidArgumentException::class);

        $this->service->updateTask(5, [
            'title' => 'test',
            'start_date' => '2026-08-10',
            'due_date' => '2026-08-09'
        ]);
    }

    public function testUpdateTaskReturnsTheRepositoryResult(): void
    {
        $this->repository->method('findById')->willReturn(['id' => 5]);

        $this->repository->method('update')->willReturn(true);

        $result = $this->service->updateTask(5, [
            'title' => 'test',
        ]);

        $this->assertTrue($result);
    }

    public function testUpdateTaskDoesNotWriteWhenTaskDoesNotExist(): void
    {
        $repository = $this->createMock(TaskRepository::class);

        $repository->method('findById')->willReturn(null);

        $service = new TaskService($repository);

        $repository->expects($this->never())->method('update');

        $this->expectException(TaskNotFoundException::class);

        $service->updateTask(100, ['title' => 'test']);
    }
}
