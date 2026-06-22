<?php

namespace App\Middlewares;

use App\Exceptions\UnauthorizedException;
use App\Repositories\RolesRepository;
use App\Repositories\SessionsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use function strtotime;

readonly class AuthMiddleware implements MiddlewareInterface {

    public function __construct(
        private SessionsRepository $session_repo,
        private RolesRepository    $role_repo
    ) {}

    public function process(Request $request, Handler $handler): Response {
        // Get token from header
        $header = $request->getHeaderLine('Authorization');
        if (!preg_match('/Bearer\s(\S+)/', $header, $matches))
            throw new UnauthorizedException('Token missing');

        // Get plain token and hash it
        $plain_token = $matches[1];
        $hash_token = hash('sha256', $plain_token);

        // Find session by access token
        $session = $this->session_repo->findByAccessToken($hash_token);
        if (!$session)
            throw new UnauthorizedException('Invalid token');

        // Check if access token is expired
        if (strtotime($session['access_expires_at']) < time())
            throw new UnauthorizedException('Token expired');

        // Get role by user_id
        $role_id = $this->role_repo->findByUserId($session['user_id']);

        // Set attributes to request
        $request = $request->withAttribute('user_id', $session['user_id']);
        $request = $request->withAttribute('role_id', $role_id);

        // Continue request
        return $handler->handle($request);
    }
}