<?php

namespace App\Repository;

use PDO;

class TaskRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(int $user_id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE creator_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    public function findById(int $id, int $user_id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id AND creator_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function create(int $user_id, array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tasks (title, description, creator_id) VALUES (:title, :description, :creator_id)"
        );
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'creator_id' => $user_id
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $task_id, int $user_id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE tasks SET title = :title, description = :description WHERE id = :task_id AND creator_id = :user_id"
        );
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'task_id' => $task_id,
            'user_id' => $user_id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $task_id, int $user_id): bool 
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM tasks WHERE id = :id AND creator_id = :user_id"
        );
        $stmt->execute([
                'id' => $task_id,
                'user_id' => $user_id
            ]);
        return $stmt->rowCount() > 0;
    }
}
