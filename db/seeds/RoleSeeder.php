<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RoleSeeder extends AbstractSeed {
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

        $table = $this->table('roles');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
