<?php

namespace App\Actions\Users;

use App\Actions\BAction;
use App\DTOs\UserDto;
use App\Repositories\UsersRepository;
use App\Services\PermissionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_map;

class ListUsersAction extends BAction {
    protected string $requiredPermission = 'user.list';

    public function __construct(
        PermissionService                $permission_service,
        private readonly UsersRepository $repo
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
        $users = array_map(fn($user) => UserDto::fromArray($user), $data);

        return $this->json($response, [
            'users' => $users,
            'totalItem' => $totalItem
        ]);
    }
}