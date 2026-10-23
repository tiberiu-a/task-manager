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

        $this->service->createTask(5, []);
    }

    public function testCreateTaskReturnsTheNewId(): void
    {
        $this->repository->method('create')->willReturn(42);

        $result = $this->service->createTask(5, [
            'title' => 'Test',
        ]);

        $this->assertSame(42, $result);
    }

    public function testCreateTaskPassesTheLoggedInUserAsCreator(): void
    {
        $repository = $this->createMock(TaskRepository::class);
        $service = new TaskService($repository);

        $data = [
            'title' => 'Test',
            'creator_id' => 99
        ];

        $repository->expects($this->once())
            ->method('create')
            ->with(7);

        $service->createTask(7, $data);
    }

    public function testGetTaskReturnsTheTaskFoundForTheUser(): void
    {
        $repository = $this->createMock(TaskRepository::class);
        $service = new TaskService($repository);

        $data = [
            'id' => 5,
            'title' => 'Test',
        ];

        $repository->method('findById')->willReturn($data);

        $repository->expects($this->once())
            ->method('findById')
            ->with(5, 7);

        $returned_data = $service->getTask(5, 7);

        $this->assertSame($data, $returned_data);
    }

    public function testGetTaskThrowsWhenNoTaskIsFoundForTheUser(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(TaskNotFoundException::class);

        $this->service->getTask(5, 7);
    }

    public function testUpdateTaskUpdatesTheTaskFoundForTheUser(): void
    {
        $repository = $this->createMock(TaskRepository::class);
        $service = new TaskService($repository);

        $data = [
            'id' => 5,
            'title' => 'Test',
        ];

        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($data)
            ->with(5, 7);


        $new_data = [
            'id' => 5,
            'title' => 'New Test',
        ];

        $repository->expects($this->once())
            ->method('update')
            ->with(5, 7, $new_data);

        $service->updateTask(5, 7, $new_data);
    }

    public function testUpdateTaskThrowsWhenTheTaskIsNotOwnedByTheUser(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(TaskNotFoundException::class);

        $this->service->updateTask(5, 999, []);
    }

    public function testUpdateTaskRejectsEmptyTitle(): void
    {
        $this->repository->method('findById')->willReturn(['id' => 5]);

        $this->expectException(InvalidArgumentException::class);

        $this->service->updateTask(5, 5, []);
    }

    public function testUpdateTaskDoesNotWriteWhenTaskDoesNotExist(): void
    {
        $repository = $this->createMock(TaskRepository::class);

        $repository->method('findById')->willReturn(null);

        $service = new TaskService($repository);

        $repository->expects($this->never())->method('update');

        $this->expectException(TaskNotFoundException::class);

        $service->updateTask(100, 5, ['title' => 'test']);
    }

    public function testDeleteTaskThrowsWhenNoTaskIsFoundForTheUser(): void
    {
        $repository = $this->createMock(TaskRepository::class);
        $service = new TaskService($repository);

        $repository->method('delete')->willReturn(false);

        $repository->expects($this->once())
            ->method('delete')
            ->with(5, 999);

        $this->expectException(TaskNotFoundException::class);

        $service->deleteTask(5, 999);
    }

    public function testDeleteTaskDeletesTheTaskFoundForTheUser(): void
    {
        $repository = $this->createMock(TaskRepository::class);
        $service = new TaskService($repository);

        $repository->method('delete')->willReturn(true);

        $repository->expects($this->once())
            ->method('delete')
            ->with(5, 7);

        $service->deleteTask(5, 7);
    }

    public function testListTasksReturnsTheTasksFoundForTheUser(): void
    {
        $repository = $this->createMock(TaskRepository::class);
        $service = new TaskService($repository);

        $data = [
            [
                'id' => 5,
                'title' => 'Test',
                'description' => 'Test description'
            ],
            [
                'id' => 6,
                'title' => 'Test 1',
                'description' => 'Test description 1'
            ]
        ];

        $repository->method('findAll')->willReturn($data);

        $repository->expects($this->once())
            ->method('findAll')
            ->with(7);

        $returned_data = $service->listTasks(7);

        $this->assertSame($data, $returned_data);
    }
}
