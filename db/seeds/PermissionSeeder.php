<?php

declare(strict_types=1);

class PermissionSeeder extends BSeeder {
    public function run(): void {
        $data = [
            // Book perms
            [
                'id' => 12,
                'code' => 'book.create'
            ],
            [
                'id' => 13,
                'code' => 'book.read'
            ],
            [
                'id' => 14,
                'code' => 'book.update'
            ],
            [
                'id' => 15,
                'code' => 'book.delete'
            ],

            // Permission perms
            [
                'id' => 10,
                'code' => 'permission.grant'
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

            // User perms
            [
                'id' => 4,
                'code' => 'user.create'
            ],
            [
                'id' => 1,
                'code' => 'user.read'
            ],
            [
                'id' => 11,
                'code' => 'user.list'
            ],
            [
                'id' => 2,
                'code' => 'user.update'
            ],
            [
                'id' => 3,
                'code' => 'user.delete'
            ],
        ];

        $this->seed('permissions', $data);
    }
}
