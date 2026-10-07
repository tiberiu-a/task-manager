<?php

namespace App\Service;

use App\Exception\InvalidCredentialsException;
use App\Repository\UserRepository;
use InvalidArgumentException;
use DateTime;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data): int
    {
        $this->assertHasName($data);

        $this->assertHasEmail($data);

        $this->assertValidEmail($data);

        $this->assertHasPassword($data);

        $this->assertValidLengthPassword($data);

        $birthday = $this->validateDate($data['birthday'] ?? null);

        $this->assertValidBirthday($birthday);

        $data['password'] = $this->hashPassword($data);

        return $this->userRepository->create($data);
    }

    public function loginUser(array $data): int
    {
        $this->assertHasEmail($data);

        $this->assertValidEmail($data);
        
        $this->assertHasPassword($data);

        $user = $this->verifyUserEmail($data);

        $this->verifyUserPassword($data['password'], $user['password']);

        return $user['id'];
    }

    private function verifyUserEmail(array $data): array 
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if ($user === null) {
            throw new InvalidCredentialsException("Invalid email address or incorrect password");
        }

        return $user;
    }

    private function verifyUserPassword(string $plain_password, string $hashed_password): void
    {
        $isUserPassValid = password_verify($plain_password, $hashed_password);

        if ($isUserPassValid === false) {
            throw new InvalidCredentialsException("Invalid email address or incorrect password");
        }
    }

    private function assertHasName(array $data): void
    {
        if (empty($data['name'])) {
            throw new InvalidArgumentException("The name field is required");
        }
    }

    private function assertHasEmail(array $data): void
    {
        if (empty($data['email'])) {
            throw new InvalidArgumentException("The email field is required");
        }
    }

    private function assertValidEmail(array $data): void
    {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }
    }

    private function assertHasPassword(array $data): void
    {
        if (empty($data['password'])) {
            throw new InvalidArgumentException("The password field is required");
        }
    }

    private function assertValidLengthPassword(array $data): void
    {
        if (strlen($data['password']) < 8 || strlen($data['password']) > 40) {
            throw new InvalidArgumentException("Password needs to be between 8 and 40 characters");
        }
    }

    private function hashPassword(array $data): string
    {
        return password_hash($data['password'], PASSWORD_DEFAULT);
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

    private function assertValidBirthday(?DateTime $birthday): void
    {
        if ($birthday !== null) {
            $currentDate = new DateTime('now');
            $minDate = new DateTime('-120 years');
            if ($birthday > $currentDate || $birthday < $minDate) {
                throw new InvalidArgumentException("Your age can't be bigger than 120 years");
            }
        }
    }    
}
