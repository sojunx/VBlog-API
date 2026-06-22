<?php

namespace App\Actions\Users;

use App\Actions\BAction;
use App\Services\PermissionService;
use App\Services\SessionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function sprintf;

class LogoutUserAction extends BAction {

    public function __construct(
        PermissionService               $permission_service,
        private readonly SessionService $session_service
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        // Revoke the session by user id and refresh token
        $user_id = $request->getAttribute('user_id');
        $hashed_access_token = $request->getAttribute('hashed_access_token');
        $this->session_service->revoke($user_id, $hashed_access_token);

        // Clear the cookies
        $at_cookie = sprintf('access_token=; Max-Age=0; Path=%s; HttpOnly; Secure; SameSite=Strict', $this->SESSION_PATH);
        $rt_cookie = sprintf('refresh_token=; Max-Age=0; Path=%s; HttpOnly; Secure; SameSite=Strict', $this->SESSION_PATH);
        
        return $this->json($response, ['message' => 'You have been logged out'])
            ->withAddedHeader('Set-Cookie', $at_cookie)
            ->withAddedHeader('Set-Cookie', $rt_cookie);
    }
}