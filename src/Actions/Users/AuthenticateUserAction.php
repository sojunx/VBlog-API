<?php

namespace App\Actions\Users;

use App\Actions\BAction;
use App\DTOs\UserDto;
use App\Exceptions\ValidationException;
use App\Repositories\UsersRepository;
use App\Services\PermissionService;
use App\Services\SessionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class  AuthenticateUserAction extends BAction {
    public function __construct(
        private readonly UsersRepository $repo,
        private readonly SessionService  $session_service,
        PermissionService                $permission_service
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $data = (array)$request->getParsedBody();

        if (empty($data['email']) || empty($data['password']))
            throw new ValidationException('Email and password are required');

        $user = $this->repo->findByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password_hash']))
            throw new ValidationException('Invalid credentials');

        $session = $this->session_service->generate($user['id']);

        $at_cookie = sprintf(
            'access_token=%s; Expires=%s; Path=%s; HttpOnly; Secure; SameSite=Strict',
            $session['access_token']['token'],
            gmdate('D, d M Y H:i:s \G\M\T', $session['access_token']['cookie_expires_at']),
            $this->SESSION_PATH
        );

        $rt_cookie = sprintf(
            'refresh_token=%s; Expires=%s; Path=%s; HttpOnly; Secure; SameSite=Strict',
            $session['refresh_token']['token'],
            gmdate('D, d M Y H:i:s \G\M\T', $session['refresh_token']['cookie_expires_at']),
            $this->SESSION_PATH
        );

        $dto = UserDto::fromArray($user);
        return $this->json($response, ['message' => 'You are logged in', 'user' => $dto])
            ->withAddedHeader('Set-Cookie', $at_cookie)
            ->withAddedHeader('Set-Cookie', $rt_cookie);
    }
}