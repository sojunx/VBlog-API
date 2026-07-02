<?php

declare(strict_types=1);

class BookSeeder extends BSeeder {
    public function run(): void {
        $data = [
            [
                'id' => 1,
                'title' => 'The Silent Library',
                'author' => 'Emily Carter',
                'isbn' => '9780143127550',
                'cover_image_url' => '',
            ],
            [
                'id' => 2,
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'isbn' => '9780735211292',
                'cover_image_url' => '',
            ],
            [
                'id' => 3,
                'title' => 'Designing Your Life',
                'author' => 'Bill Burnett',
                'isbn' => '9781101875322',
                'cover_image_url' => '',
            ],
            [
                'id' => 4,
                'title' => 'The Lean Startup',
                'author' => 'Eric Ries',
                'isbn' => '9780307887894',
                'cover_image_url' => '',
            ],
            [
                'id' => 5,
                'title' => 'Steve Jobs',
                'author' => 'Walter Isaacson',
                'isbn' => '9781451648539',
                'cover_image_url' => '',
            ],
        ];

        $this->seed('books', $data);
    }
}
