<?php

namespace App\DTOs;

class PostDto {
    public function __construct(
        public int    $id,
        public string $title,
        public string $content,
        public string $created_at,
        public string $updated_at,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['title'],
            $data['content'],
            $data['created_at'],
            $data['updated_at'],
        );
    }
}