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
        $queryParams = $request->getQueryParams();
        
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : 5;

        $offset = ($page - 1) * $limit;

        $totalItem = $this->repo->countAll();

        $data = $this->repo->findAllPaginated($limit, $offset);
        $books = array_map(fn($book) => BookDto::fromArray($book), $data);

        return $this->json($response, [
            'books' => $books,
            'totalItem' => $totalItem
        ]);
    }
}
