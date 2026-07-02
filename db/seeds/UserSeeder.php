<?php

declare(strict_types=1);

class UserSeeder extends BSeeder {
    public function run(): void {
        $data = [
            [
                'id' => '27b0b78b-7b82-4903-bf07-6e2002f8d20b',
                'user_name' => 'admin',
                'email' => 'admin@localhost.com',
                'password_hash' => password_hash('admin', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '6382deb1-a8d5-408c-a307-24e53efecad9',
                'user_name' => 'user',
                'email' => 'user@localhost.com',
                'password_hash' => password_hash('user', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '6daa1b2e-cfd8-4f58-8567-7d041f6815f6',
                'user_name' => 'tester',
                'email' => 'tester@localhost.com',
                'password_hash' => password_hash('tester', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '8ed0c3aa-796d-4dfd-a798-784fc12fa2ee',
                'user_name' => 'support',
                'email' => 'support@localhost.com',
                'password_hash' => password_hash('support', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->seed('users', $data);
    }
}
