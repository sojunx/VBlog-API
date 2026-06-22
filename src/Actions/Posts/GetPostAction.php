<?php

namespace App\Actions\Posts;

use App\Actions\BAction;
use App\DTOs\PostDto;
use App\Repositories\PostsRepository;
use App\Services\PermissionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPostAction extends BAction {
    public function __construct(
        PermissionService                $permission_service,
        private readonly PostsRepository $repo
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $data = $this->repo->findById($args['id']);
        $post = PostDto::fromArray($data);

        return $this->json($response, $post);
    }
}