<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CategorySeeder extends AbstractSeed {
    public function run(): void {
        $data = [
            [
                'id' => 1,
                'name' => 'Development'
            ],
            [
                'id' => 2,
                'name' => 'Design'
            ],
            [
                'id' => 3,
                'name' => 'Marketing'
            ]
        ];

        $table = $this->table('categories');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
