<?php

namespace App\Repositories;

use PDO;

readonly class PermissionsRepository {
    public function __construct(private PDO $db) {}

    public function findByRoleId(int $role_id): array {
        $sql = '
            SELECT code
            FROM permissions p 
            JOIN role_permissions rp ON p.id = rp.permission_id 
            WHERE rp.role_id = ?
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role_id]);

        $result = $stmt->fetchAll();
        return $result ?: [];
    }
}