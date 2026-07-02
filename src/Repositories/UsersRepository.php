<?php

namespace App\Repositories;

use PDO;
use Ramsey\Uuid\Uuid;

readonly class UsersRepository {
    public function __construct(private PDO $db) {}

    public function insert(string $email, string $password_hash): string {
        $user_id = Uuid::uuid4()->toString();
        $user_name = explode('@', $email)[0];

        $sql = 'INSERT INTO users (id, email, password_hash, user_name) VALUES (?, ?, ?, ?)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $email, $password_hash, $user_name]);

        return $user_id;
    }

    public function countAll(): int {
        $sql = 'SELECT COUNT(id) FROM users';
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    public function findAllPaginated(int $limit, int $offset): array {
        $sql = 'SELECT u.*, r.code AS role 
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                LIMIT :limit OFFSET :offset';
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

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

    public function updateAvatar(string $userId, string $avatarUrl, string $avatarPublicId): void {
        $sql = 'UPDATE users SET avatar_url = ?, avatar_public_id = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$avatarUrl, $avatarPublicId, $userId]);
    }

    public function updateUsername(string $userId, string $username): void {
        $sql = 'UPDATE users SET user_name = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $userId]);
    }

    public function updatePassword(string $userId, string $passwordHash): void {
        $sql = 'UPDATE users SET password_hash = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$passwordHash, $userId]);
    }
}