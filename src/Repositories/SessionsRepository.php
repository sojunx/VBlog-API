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

    public function findByRefreshToken(string $hash_token): ?array {
        $sql = 'SELECT * FROM sessions WHERE refresh_token_hash = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hash_token]);

        $session = $stmt->fetch();
        return $session ?: null;
    }

    public function revokeToken(string $user_id, string $hashed_access_token): void {
        $sql = 'DELETE FROM sessions WHERE user_id = ? AND access_token_hash = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $hashed_access_token]);
    }
}