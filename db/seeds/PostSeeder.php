<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed {
    public function run(): void {
        $data = [
            [
                'id' => 4,
                'title' => 'Post 1 - Development',
                'content' => 'Today, Apple is the world\'s largest software company.',
                'author_id' => '6382deb1-a8d5-408c-a307-24e53efecad9'
            ],
            [
                'id' => 5,
                'title' => 'Post 2 - Design',
                'content' => 'Yesterday, Apple released the new MacBook Pro. The design is beautiful and the performance is amazing.',
                'author_id' => '6382deb1-a8d5-408c-a307-24e53efecad9'
            ],
            [
                'id' => 6,
                'title' => 'Post 3 - Marketing',
                'content' => 'The target market of Apple products is the consumer market which is growing at a fast rate.',
                'author_id' => '6382deb1-a8d5-408c-a307-24e53efecad9'
            ]
        ];

        $table = $this->table('posts');

        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $table->truncate();
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $table->insert($data)->saveData();
    }
}
