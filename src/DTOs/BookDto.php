<?php

namespace App\DTOs;

class BookDto {
    public function __construct(
        public int     $id,
        public string  $title,
        public string  $author,
        public ?string $isbn,
        public ?string $cover_image_url,
        public string  $created_at,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            (int)$data['id'],
            $data['title'],
            $data['author'],
            $data['isbn'] ?? null,
            $data['cover_image_url'] ?? null,
            $data['created_at'],
        );
    }
}
