<?php

namespace App\Actions\Posts;

use App\Actions\BAction;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Repositories\PostsRepository;
use App\Services\PermissionService;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdatePostAction extends BAction {
    protected string $requiredPermission = 'post.update';

    public function __construct(
        PermissionService                $permission_service,
        private readonly PostsRepository $repo
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $data = (array)$request->getParsedBody();
        $user_id = $request->getAttribute('user_id');

        // Check if the post exists & is owned by the user
        $post = $this->repo->findById($args['id']);
        if (!$post)
            throw new NotFoundException('Post not found');

        if ($post['author_id'] !== $user_id)
            throw new BadRequestException('Post not owned by the user');

        try {
            $this->repo->update($post['id'], $data);
            return $this->json($response, ['message' => 'Post updated successfully']);
        } catch (PDOException $ex) {
            throw new BadRequestException('Failed to update post');
        }
    }
}