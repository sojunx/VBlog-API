<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed {
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
            [
                'id' => '27b0b78b-7b82-4903-bf07-6e2002f8d20b',
                'email' => 'admin@localhost.com',
                'password_hash' => password_hash('admin', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '6382deb1-a8d5-408c-a307-24e53efecad9',
                'email' => 'user@localhost.com',
                'password_hash' => password_hash('user', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '27b0b78b-7b82-4909-bf07-6e2102f8d20b',
                'email' => 'support@localhost.com',
                'password_hash' => password_hash('support', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $table = $this->table('users');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
