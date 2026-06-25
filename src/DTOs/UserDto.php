<?php

namespace App\DTOs;

class UserDto {
    public function __construct(
        public string $id,
        public string $email,
        public string $user_name,
        public string $created_at,
        public ?string $role = null,
        public ?string $avatar_url = null,
    ) {}

    public static function fromArray(array $data, ?string $role = null): self {
        return new self(
            $data['id'],
            $data['email'],
            $data['user_name'] ?? explode('@', $data['email'])[0],
            $data['created_at'],
            $role ?? $data['role'] ?? null,
            $data['avatar_url'] ?? null,
        );
    }
}