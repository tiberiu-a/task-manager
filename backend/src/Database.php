<?php

namespace App;

use PDO;

class Database
{
    private string $dsn;
    private string $user;
    private string $password;
    private PDO $pdo;

    public function __construct()
    {
        $this->dsn = "mysql:host=". getenv('DB_HOST') .";dbname=". getenv('DB_NAME') .";charset=utf8mb4";
        $this->user = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');
        $this->pdo = new PDO($this->dsn, $this->user, $this->password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
