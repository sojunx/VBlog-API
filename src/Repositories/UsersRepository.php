<?php

namespace App\Repositories;

use PDO;
use Ramsey\Uuid\Uuid;

readonly class UsersRepository {
    public function __construct(private PDO $db) {}

    public function insert(string $email, string $password_hash): string {
        $user_id = Uuid::uuid4()->toString();

        $sql = 'INSERT INTO users (id, email, password_hash) VALUES (?, ?, ?)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $email, $password_hash]);

        return $user_id;
    }

    public function findAll(): array {
        $sql = 'SELECT * FROM users';
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function findByEmail(string $email): ?array {
        $sql = 'SELECT * FROM users WHERE email = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }

    public function findById(string $id): ?array {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }
}