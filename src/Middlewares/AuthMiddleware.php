<?php

namespace App\Middlewares;

use App\Exceptions\GoneException;
use App\Exceptions\UnauthorizedException;
use App\Repositories\RolesRepository;
use App\Repositories\SessionsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use function strtotime;
use function time;

readonly class AuthMiddleware implements MiddlewareInterface {

    public function __construct(
        private SessionsRepository $session_repo,
        private RolesRepository    $role_repo
    ) {}

    public function process(Request $request, Handler $handler): Response {
        $plain_token = null;

        // Try getting token from Cookie first
        $cookies = $request->getCookieParams();
        if (!empty($cookies['access_token']))
            $plain_token = $cookies['access_token'];
        else
            // Fallback to Authorization header
            if (preg_match('/Bearer\s(\S+)/', $request->getHeaderLine('Authorization'), $matches))
                $plain_token = $matches[1];

        if (!$plain_token)
            throw new UnauthorizedException('Missing token');

        // Get plain token and hash it
        $hash_token = hash('sha256', $plain_token);

        // Find session by access token
        $session = $this->session_repo->findByAccessToken($hash_token);
        if (!$session)
            throw new UnauthorizedException('Token invalid');

        // Check if access token is expired
        if (strtotime($session['access_expires_at']) < time())
            throw new GoneException('Access token expired');

        // Check if refresh token is expired
        if (strtotime($session['refresh_expires_at']) < time())
            throw new UnauthorizedException('Refresh token expired');

        // Get role by user_id
        $role_id = $this->role_repo->findByUserId($session['user_id']);

        // Set attributes to request
        $request = $request->withAttribute('user_id', $session['user_id']);
        $request = $request->withAttribute('hashed_access_token', $hash_token);
        $request = $request->withAttribute('role_id', $role_id);

        // Continue request
        return $handler->handle($request);
    }
}