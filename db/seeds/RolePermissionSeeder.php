<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RolePermissionSeeder extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'RoleSeeder',
            'PermissionSeeder'
        ];
    }

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
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

            // Support role perms
            [
                'role_id' => 3,
                'permission_id' => 4
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
            ]
        ];

        $table = $this->table('role_permissions');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
