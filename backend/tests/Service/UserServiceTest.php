<?php

namespace Tests\Service;

use App\Repository\UserRepository;
use App\Service\UserService;
use InvalidArgumentException;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserRepository&Stub $repository;
    private UserService $service;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = $this->createStub(UserRepository::class);
        $this->service = new UserService($this->repository);
    }

    public function testCreateUserHashesThePassword(): void
    {
        $repository = $this->createMock(UserRepository::class);
        $service = new UserService($repository);

        $data = [
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'Testul123',
        ];

        $repository->expects($this->once())
            ->method('create')
            ->with($this->callback(
                function (array $args) use ($data): bool {
                    return password_verify($data['password'], $args['password']);
                }
            ));

        $service->createUser($data);
    }

    public function testCreateUserRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("You can't create an account without a name");

        $this->service->createUser(
            [
                'email' => 'test@test.com',
                'password' => 'Testul123',
            ]
        );
    }

    public function testCreateUserRejectsEmptyEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("You can't create an account without an email");

        $this->service->createUser(
            [
                'name' => 'Test',
                'password' => 'Testul123',
            ]
        );
    }

    public function testCreateUserRejectsInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid email format");

        $this->service->createUser(
            [
                'name' => 'Test',
                'email' => 'Test email',
                'password' => 'Testul123',
            ]
        );
    }

    public function testCreateUserRejectsEmptyPassword(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("You can't create an account without a password");

        $this->service->createUser(
            [
                'name' => 'Test',
                'email' => 'test@test.com',
            ]
        );
    }

    public function testCreateUserRejectsInvalidPassword(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Password needs to be between 8 and 40 characters");

        $this->service->createUser(
            [
                'name' => 'Test',
                'email' => 'test@test.com',
                'password' => '1234',
            ]
        );
    }

    public function testCreateUserRejectsInvalidBirthday(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Your age can't be bigger than 120 years");

        $this->service->createUser(
            [
                'name' => 'Test',
                'email' => 'test@test.com',
                'password' => '123456789',
                'birthday' => '1900-01-01'
            ]
        );
    }

    public function testCreateUserRejectsDayOrMonthOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The day or month is out of range");

        $this->service->createUser(
            [
                'name' => 'Test',
                'email' => 'test@test.com',
                'password' => '123456789',
                'birthday' => '2026-01-45'
            ]
        );
    }

    public function testCreateUserRejectsInvalidDateFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The date needs to have this format Y-m-d (ex. 2000-01-30)");

        $this->service->createUser(
            [
                'name' => 'Test',
                'email' => 'test@test.com',
                'password' => '123456789',
                'birthday' => 'hello'
            ]
        );
    }
}
