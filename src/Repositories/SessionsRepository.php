<?php

namespace App\Repositories;

use PDO;

readonly class SessionsRepository {
    public function __construct(private PDO $db) {}

    public function insert(array $session): void {
        $sql = 'INSERT INTO sessions (user_id, access_token_hash, refresh_token_hash, access_expires_at, refresh_expires_at) VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($session);
    }

    public function findByAccessToken(string $hash_token): ?array {
        $sql = 'SELECT * FROM sessions WHERE access_token_hash = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hash_token]);

        $session = $stmt->fetch();
        return $session ?: null;
    }
}