<?php

declare(strict_types=1);

class RolePermissionSeeder extends BSeeder {
    public function getDependencies(): array {
        return [
            'RoleSeeder',
            'PermissionSeeder'
        ];
    }

    public function run(): void {
        $data = [
            // Admin role perms
            [
                'role_id' => 1,
                'permission_id' => 3
            ],
            [
                'role_id' => 1,
                'permission_id' => 8
            ],
            [
                'role_id' => 1,
                'permission_id' => 9
            ],
            [
                'role_id' => 1,
                'permission_id' => 10
            ],
            [
                'role_id' => 1,
                'permission_id' => 11
            ],
            [
                'role_id' => 1,
                'permission_id' => 12
            ],
            [
                'role_id' => 1,
                'permission_id' => 13
            ],
            [
                'role_id' => 1,
                'permission_id' => 14
            ],
            [
                'role_id' => 1,
                'permission_id' => 15
            ],

            // User role perms
            [
                'role_id' => 2,
                'permission_id' => 1
            ],
            [
                'role_id' => 2,
                'permission_id' => 2
            ],
            [
                'role_id' => 2,
                'permission_id' => 5
            ],
            [
                'role_id' => 2,
                'permission_id' => 6
            ],
            [
                'role_id' => 2,
                'permission_id' => 7
            ],

            // Support role perms
            [
                'role_id' => 3,
                'permission_id' => 4
            ],
        ];

        $this->seed('role_permissions', $data);
    }
}
