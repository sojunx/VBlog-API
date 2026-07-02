<?php

declare(strict_types=1);

class CategorySeeder extends BSeeder {
    public function run(): void {
        $data = [
            [
                'id' => 1,
                'title' => 'Fiction',
                'slug' => 'fiction',
            ],
            [
                'id' => 2,
                'title' => 'Self Help',
                'slug' => 'self-help',
            ],
            [
                'id' => 3,
                'title' => 'Business',
                'slug' => 'business',
            ],
            [
                'id' => 4,
                'title' => 'Technology',
                'slug' => 'technology',
            ],
            [
                'id' => 5,
                'title' => 'Biography',
                'slug' => 'biography',
            ],
        ];

        $this->seed('categories', $data);
    }
}
