<?php

namespace App\Repositories;

class BooksRepository extends AbstractRepository {
    public function create(array $data): int {
        $sql = 'INSERT INTO books (title, author, isbn, cover_image_url) VALUES (:title, :author, :isbn, :cover_image_url)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'author' => $data['author'],
            'isbn' => $data['isbn'] ?? null,
            'cover_image_url' => $data['cover_image_url'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findAll(): array {
        $sql = 'SELECT * FROM books';
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function countAll(): int {
        $sql = 'SELECT COUNT(id) FROM books';
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    public function findAllPaginated(int $limit, int $offset): array {
        $sql = 'SELECT * FROM books LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $sql = 'SELECT * FROM books WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function findByIsbn(string $isbn): ?array {
        $sql = 'SELECT * FROM books WHERE isbn = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$isbn]);

        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): void {
        $sql = 'DELETE FROM books WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }

    public function update(int $id, array $data): void {
        $fields = [];
        $params = ['id' => $id];

        foreach (['title', 'author', 'isbn', 'cover_image_url'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

        $sql = 'UPDATE books SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }
}
