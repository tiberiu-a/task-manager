<?php

namespace Tests\Service;

use App\Exception\InvalidCredentialsException;
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
        $this->expectExceptionMessage("The name field is required");

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
        $this->expectExceptionMessage("The email field is required");

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
        $this->expectExceptionMessage("The password field is required");

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

    public function testLoginUserRejectsEmptyEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The email field is required");

        $this->service->loginUser(
            [
                'password' => 'Testul123',
            ]
        );
    }

    public function testLoginUserRejectEmptyPassword(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The password field is required");

        $this->service->loginUser(
            [
                'email' => 'test@test.com',
            ]
        );
    }

    public function testLoginUserRejectInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid email format");

        $this->service->loginUser(
            [
                'email' => 'test email',
                'password' => '123456789',
            ]
        );
    }

    public function testLoginUserRejectUnregisteredUser(): void
    {
        $repository = $this->createStub(UserRepository::class);
        $repository->method('findByEmail')->willReturn(null);

        $service = new UserService($repository);

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage("Invalid email address or incorrect password");
        $service->loginUser(
            [
                'email' => 'nobody@test.com',
                'password' => '123456789',
            ]
        );
    }

    public function testLoginUserRejectWrongPassword(): void
    {
        $hash_pass = password_hash('correctPassword', PASSWORD_DEFAULT);
        $repository = $this->createStub(UserRepository::class);
        $repository->method('findByEmail')->willReturn(
            [
                'email' => 'validEmail@test.com',
                'password' => $hash_pass,
            ]
        );

        $service = new UserService($repository);

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage("Invalid email address or incorrect password");
        $service->loginUser(
            [
                'email' => 'validEmail@test.com',
                'password' => 'wrongPassword',
            ]
        );
    }

    public function testLoginUserAcceptsCorrectCredentials(): void
    {
        $hash_pass = password_hash('correctPassword', PASSWORD_DEFAULT);
        $repository = $this->createStub(UserRepository::class);
        $repository->method('findByEmail')->willReturn(
            [
                'id' => 10,
                'email' => 'validEmail@test.com',
                'password' => $hash_pass,
            ]
        );

        $service = new UserService($repository);

        $user_id = $service->loginUser(
            [                
                'email' => 'validEmail@test.com',
                'password' => 'correctPassword',
            ]
        );

        $this->assertSame(10,$user_id);
    }
}
