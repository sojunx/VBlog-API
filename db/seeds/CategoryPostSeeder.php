<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CategoryPostSeeder extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'UserSeeder',
            'CategorySeeder',
            'PostSeeder'
        ];
    }

    public function run(): void {
        $data = [
            [
                'category_id' => 1,
                'post_id' => 4
            ],
            [
                'category_id' => 2,
                'post_id' => 5
            ],
            [
                'category_id' => 3,
                'post_id' => 6
            ]
        ];

        $table = $this->table('category_posts');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
