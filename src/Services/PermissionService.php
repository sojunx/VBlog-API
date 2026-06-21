<?php

namespace App\Services;

use App\Repositories\PermissionsRepository;
use function in_array;

readonly class PermissionService {
    public function __construct(private PermissionsRepository $repo) {}

    public function hasPermissions(int $role_id, string $required_permissions): bool {
        $permissions = $this->repo->findByRoleId($role_id);
        $codes = array_column($permissions, 'code');

        return in_array($required_permissions, $codes, true);
    }
}