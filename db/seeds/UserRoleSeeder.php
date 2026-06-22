<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class UserRoleSeeder extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'UserSeeder',
            'RoleSeeder'
        ];
    }

    public function run(): void {
        $data = [
            [
                'user_id' => '27b0b78b-7b82-4903-bf07-6e2002f8d20b',
                'role_id' => 1
            ],
            [
                'user_id' => '6382deb1-a8d5-408c-a307-24e53efecad9',
                'role_id' => 2
            ],
            [
                'user_id' => '6daa1b2e-cfd8-4f58-8567-7d041f6815f6',
                'role_id' => 2
            ],
            [
                'user_id' => '8ed0c3aa-796d-4dfd-a798-784fc12fa2ee',
                'role_id' => 3
            ]
        ];

        $table = $this->table('user_roles');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
