<?php

namespace App\Repositories;

class RolesRepository extends AbstractRepository {

    public function findByUserId(string $user_id): int {
        $sql = 'SELECT * FROM user_roles WHERE user_id = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);

        $result = $stmt->fetch();
        return $result['role_id'];
    }

    public function findCodeById(int $role_id): ?string {
        $sql = 'SELECT code FROM roles WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role_id]);
        $result = $stmt->fetch();
        return $result ? $result['code'] : null;
    }

    public function findRoleCodeByUserId(string $user_id): ?string {
        $sql = 'SELECT r.code FROM roles r 
                JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result ? $result['code'] : null;
    }

    public function assignRole(string $user_id, int $role_id): void {
        $sql = 'INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $role_id]);
    }
}