<?php

namespace App\Actions\Users;

use App\Actions\BAction;
use App\Exceptions\ConflictException;
use App\Exceptions\ValidationException;
use App\Repositories\RolesRepository;
use App\Repositories\UsersRepository;
use App\Services\PermissionService;
use Fig\Http\Message\StatusCodeInterface as HTTP;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function password_hash;
use const PASSWORD_DEFAULT;

class CreateUserAction extends BAction {
    public function __construct(
        PermissionService                $permission_service,
        private readonly PDO             $db,
        private readonly UsersRepository $repo,
        private readonly RolesRepository $role_repo
    ) {
        parent::__construct($permission_service);
    }

    public function handle(Request $request, Response $response, array $args): Response {
        $data = (array)$request->getParsedBody();

        if (empty($data['email']) || empty($data['password']) || empty($data['confirm_password']))
            throw new ValidationException('Email, password and confirm password are required');

        if ($data['password'] !== $data['confirm_password'])
            throw new ValidationException('Passwords do not match');

        $this->db->beginTransaction();

        try {
            // Insert user to database
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $user_id = $this->repo->insert($data['email'], $password_hash);

            // Assign user role
            $this->role_repo->assignRole($user_id, 2);

            $this->db->commit();

            return $this->json($response, ['message' => 'User created'], HTTP::STATUS_CREATED);
        } catch (PDOException $ex) {
            $this->db->rollBack();

            if ($ex->getCode() === 23000)
                throw new ConflictException('Email already exists');

            throw $ex;
        }
    }
}