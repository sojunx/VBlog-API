<?php

declare(strict_types=1);

class CategoryBookSeeder extends BSeeder {
    public function getDependencies(): array {
        return [
            'BookSeeder',
            'CategorySeeder',
        ];
    }

    public function run(): void {
        $data = [
            [
                'category_id' => 1, // Fiction
                'book_id' => 1, // The Silent Library
            ],
            [
                'category_id' => 2, // Self Help
                'book_id' => 2, // Atomic Habits
            ],
            [
                'category_id' => 2, // Self Help
                'book_id' => 3, // Designing Your Life
            ],
            [
                'category_id' => 3, // Business
                'book_id' => 4, // The Lean Startup
            ],
            [
                'category_id' => 4, // Technology
                'book_id' => 4, // The Lean Startup
            ],
            [
                'category_id' => 5, // Biography
                'book_id' => 5, // Steve Jobs
            ],
            [
                'category_id' => 4, // Technology
                'book_id' => 5, // Steve Jobs
            ],
        ];

        $this->seed('category_books', $data);
    }
}
