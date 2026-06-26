<?php

namespace App\Actions\Books;

use App\Actions\BAction;
use App\DTOs\BookDto;
use App\Repositories\BooksRepository;
use App\Services\PermissionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListBooksAction extends BAction {
    protected string $requiredPermission = 'book.read';

    public function __construct(
        PermissionService $permission_service,
        private readonly BooksRepository $repo
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $data = $this->repo->findAll();
        $books = array_map(fn($book) => BookDto::fromArray($book), $data);

        return $this->json($response, $books);
    }
}
