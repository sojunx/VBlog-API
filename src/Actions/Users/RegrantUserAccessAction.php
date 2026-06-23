<?php

namespace App\Actions\Users;

use App\Actions\BAction;
use App\Exceptions\BadRequestException;
use App\Services\PermissionService;
use App\Services\SessionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function gmdate;
use function sprintf;

class RegrantUserAccessAction extends BAction {
    public function __construct(
        PermissionService               $permission_service,
        private readonly SessionService $session_service
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $cookies = $request->getCookieParams();
        $plain_refresh_token = $cookies['refresh_token'] ?? null;
        if (!$plain_refresh_token)
            throw new BadRequestException('Refresh token is required');

        $hashed_refresh_token = hash('sha256', $plain_refresh_token);
        $session = $this->session_service->regrant($hashed_refresh_token);

        $at_cookie = sprintf(
            'access_token=%s; Expires=%s; Path=%s; HttpOnly; Secure; SameSite=Strict',
            $session['access_token']['token'],
            gmdate('D, d M Y H:i:s \G\M\T', $session['access_token']['expires_at']),
            $this->SESSION_PATH
        );

        $rt_cookie = sprintf(
            'refresh_token=%s; Expires=%s; Path=%s; HttpOnly; Secure; SameSite=Strict',
            $session['refresh_token']['token'],
            gmdate('D, d M Y H:i:s \G\M\T', $session['refresh_token']['expires_at']),
            $this->SESSION_PATH
        );

        return $this->json($response, ['access_token' => $session['access_token']['token']])
            ->withAddedHeader('Set-Cookie', $at_cookie)
            ->withAddedHeader('Set-Cookie', $rt_cookie);
    }
}