<?php

namespace App\Actions\Books;

use App\Actions\BAction;
use App\Exceptions\ConflictException;
use App\Exceptions\ValidationException;
use App\Repositories\BooksRepository;
use App\Services\PermissionService;
use Fig\Http\Message\StatusCodeInterface as HTTP;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateBookAction extends BAction {
    protected string $requiredPermission = 'book.create';

    public function __construct(
        PermissionService $permission_service,
        private readonly BooksRepository $repo
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $data = (array)$request->getParsedBody();

        if (empty($data['title']) || empty($data['author']) || empty($data['isbn']) || empty($data['cover_image_url'])) {
            throw new ValidationException('Title, Author, ISBN and Cover Image URL are required');
        }

        // If ISBN is provided, check if it already exists to prevent duplication
        if (!empty($data['isbn'])) {
            $existingBook = $this->repo->findByIsbn($data['isbn']);
            if ($existingBook) {
                throw new ConflictException('A book with this ISBN already exists');
            }
        }

        $bookId = $this->repo->create([
            'title' => $data['title'],
            'author' => $data['author'],
            'isbn' => $data['isbn'] ?? null,
            'cover_image_url' => $data['cover_image_url'] ?? null
        ]);

        $createdBook = $this->repo->findById($bookId);

        return $this->json($response, [
            'message' => 'Book created successfully',
            'book' => $createdBook
        ], HTTP::STATUS_CREATED);
    }
}
