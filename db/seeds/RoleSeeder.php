<?php

declare(strict_types=1);

class RoleSeeder extends BSeeder {
    public function run(): void {
        $data = [
            [
                'id' => 1,
                'code' => 'admin'
            ],
            [
                'id' => 2,
                'code' => 'user'
            ],
            [
                'id' => 3,
                'code' => 'support'
            ]
        ];

        $this->seed('roles', $data);
    }
}
