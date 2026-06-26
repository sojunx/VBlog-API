<?php

namespace App\Actions\Books;

use App\Actions\BAction;
use App\Exceptions\BadRequestException;
use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Repositories\BooksRepository;
use App\Services\PermissionService;
use Fig\Http\Message\StatusCodeInterface as HTTP;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateBookAction extends BAction {
    protected string $requiredPermission = 'book.update';

    public function __construct(
        PermissionService $permission_service,
        private readonly BooksRepository $repo
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $id = (int)$args['id'];
        $book = $this->repo->findById($id);

        if (!$book) {
            throw new NotFoundException('Book not found');
        }

        $data = (array)$request->getParsedBody();
        $allowedFields = ['title', 'author', 'isbn', 'cover_image_url'];
        $filteredData = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $filteredData[$field] = $data[$field];
            }
        }

        // Validate values if they are being updated
        if (array_key_exists('title', $filteredData)) {
            $filteredData['title'] = trim((string)$filteredData['title']);
            if (empty($filteredData['title'])) {
                throw new ValidationException('Title cannot be empty');
            }
        }

        if (array_key_exists('author', $filteredData)) {
            $filteredData['author'] = trim((string)$filteredData['author']);
            if (empty($filteredData['author'])) {
                throw new ValidationException('Author cannot be empty');
            }
        }

        if (array_key_exists('isbn', $filteredData)) {
            $filteredData['isbn'] = trim((string)$filteredData['isbn']);
            if (empty($filteredData['isbn'])) {
                throw new ValidationException('ISBN cannot be empty');
            }
            // Check if another book already has this ISBN
            $existingBook = $this->repo->findByIsbn($filteredData['isbn']);
            if ($existingBook && (int)$existingBook['id'] !== $id) {
                throw new ConflictException('A book with this ISBN already exists');
            }
        }

        if (array_key_exists('cover_image_url', $filteredData)) {
            $filteredData['cover_image_url'] = trim((string)$filteredData['cover_image_url']);
            if (empty($filteredData['cover_image_url'])) {
                throw new ValidationException('Cover Image URL cannot be empty');
            }
        }

        if (empty($filteredData)) {
            return $this->json($response, [
                'message' => 'No fields to update'
            ]);
        }

        $this->repo->update($id, $filteredData);

        return $this->json($response, [
            'message' => 'Book updated successfully'
        ]);
    }
}
