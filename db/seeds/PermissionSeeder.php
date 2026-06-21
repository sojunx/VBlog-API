<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class PermissionSeeder extends AbstractSeed {
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
            // User perms
            [
                'id' => 1,
                'code' => 'user.read'
            ],
            [
                'id' => 2,
                'code' => 'user.update'
            ],
            [
                'id' => 3,
                'code' => 'user.delete'
            ],
            [
                'id' => 4,
                'code' => 'user.create'
            ],

            // Post perms
            [
                'id' => 5,
                'code' => 'post.read'
            ],
            [
                'id' => 6,
                'code' => 'post.update'
            ],
            [
                'id' => 7,
                'code' => 'post.delete'
            ],

            // Role perms
            [
                'id' => 8,
                'code' => 'role.create'
            ],
            [
                'id' => 9,
                'code' => 'role.assign'
            ],

            // Grant perm
            [
                'id' => 10,
                'code' => 'permission.grant'
            ],

            [
                'id' => 11,
                'code' => 'user.list'
            ]
        ];

        $table = $this->table('permissions');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
