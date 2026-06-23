<?php

namespace App\Repositories;

class PostsRepository extends AbstractRepository {
    public function findAll(): array {
        $sql = 'SELECT * FROM posts';
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $sql = 'SELECT * FROM posts WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): void {
        $sql = 'UPDATE posts SET title = :title, content = :content WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge(['id' => $id], $data));
    }
}