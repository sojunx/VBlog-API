<?php

namespace App\Actions\Users;

use App\Actions\BAction;
use App\DTOs\UserDto;
use App\Repositories\UsersRepository;
use App\Services\PermissionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetUserAction extends BAction {
    public function __construct(
        PermissionService                $permission_service,
        private readonly UsersRepository $repo
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $user_id = $request->getAttribute('user_id');
        $user = $this->repo->findById($user_id);

        $dto = UserDto::fromArray($user);
        return $this->json($response, ['message' => 'User found', 'user' => $dto]);
    }
}