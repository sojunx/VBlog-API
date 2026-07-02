<?php

namespace App\Actions\Books;

use App\Actions\BAction;
use App\DTOs\BookDto;
use App\Exceptions\NotFoundException;
use App\Repositories\BooksRepository;
use App\Services\PermissionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBookAction extends BAction {
    protected string $requiredPermission = 'book.read';

    public function __construct(
        PermissionService $permission_service,
        private readonly BooksRepository $repo
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $id = (int)$args['id'];
        $data = $this->repo->findById($id);

        if (!$data) {
            throw new NotFoundException('Book not found');
        }

        $book = BookDto::fromArray($data);

        return $this->json($response, $book);
    }
}
