<?php

namespace App\Actions\Books;

use App\Actions\BAction;
use App\Exceptions\NotFoundException;
use App\Repositories\BooksRepository;
use App\Services\PermissionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteBookAction extends BAction {
    protected string $requiredPermission = 'book.delete';

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

        $this->repo->delete($id);

        return $this->json($response, [
            'message' => 'Book deleted successfully'
        ]);
    }
}
