<?php

declare(strict_types=1);

class PostSeeder extends BSeeder {
    public function getDependencies(): array {
        return [
            'UserSeeder',
            'BookSeeder',
        ];
    }

    public function run(): void {
        $data = [
            [
                'id' => 1,
                'author_id' => '6382deb1-a8d5-408c-a307-24e53efecad9',
                'book_id' => 1,
                'title' => 'A Quiet Journey Through The Silent Library',
                'slug' => 'a-quiet-journey-through-the-silent-library',
                'rating' => 4.5,
                'summary' => 'A thoughtful review about mystery, memory, and the charm of forgotten books.',
                'content' => 'The Silent Library creates a calm but mysterious atmosphere. Its characters feel gentle, yet the story slowly reveals emotional depth.',
                'status' => 'published',
                'view_count' => 128,
                'published_at' => '2026-06-20 09:00:00',
                'created_at' => '2026-06-20 08:30:00',
                'updated_at' => '2026-06-20 09:00:00',
            ],
            [
                'id' => 2,
                'author_id' => '6382deb1-a8d5-408c-a307-24e53efecad9',
                'book_id' => 2,
                'title' => 'Small Habits, Big Changes',
                'slug' => 'small-habits-big-changes',
                'rating' => 5.0,
                'summary' => 'A practical review of how Atomic Habits explains behavior change in a simple way.',
                'content' => 'Atomic Habits is easy to read and highly practical. The book focuses on small daily improvements that compound over time.',
                'status' => 'published',
                'view_count' => 342,
                'published_at' => '2026-06-21 10:15:00',
                'created_at' => '2026-06-21 09:50:00',
                'updated_at' => '2026-06-21 10:15:00',
            ],
            [
                'id' => 3,
                'author_id' => '6382deb1-a8d5-408c-a307-24e53efecad9',
                'book_id' => 4,
                'title' => 'Why The Lean Startup Still Matters',
                'slug' => 'why-the-lean-startup-still-matters',
                'rating' => 4.0,
                'summary' => 'A review about startup thinking, fast experiments, and building products users actually need.',
                'content' => 'The Lean Startup remains useful for founders and product teams. Its strongest idea is learning quickly through validated experiments.',
                'status' => 'draft',
                'view_count' => 0,
                'published_at' => null,
                'created_at' => '2026-06-22 14:00:00',
                'updated_at' => '2026-06-22 14:00:00',
            ],
        ];

        $this->seed('posts', $data);
    }
}
