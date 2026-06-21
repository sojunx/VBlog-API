<?php

namespace App\DTOs;

class UserDto {
    public function __construct(
        public string $id,
        public string $email,
        public string $created_at,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['email'],
            $data['created_at'],
        );
    }
}