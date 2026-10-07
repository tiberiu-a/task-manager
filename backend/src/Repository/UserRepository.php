<?php

namespace App\Repository;

use App\Exception\EmailAlreadyExistsException;
use PDO;
use PDOException;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, password, birthday) VALUES (:name, :email, :password, :birthday)"
        );
        try {
            $stmt->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'birthday' => $data['birthday'] ?? null
            ]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                throw new EmailAlreadyExistsException("This email is already used!");
            }
            throw $e;
        }
        return (int) $this->pdo->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}
