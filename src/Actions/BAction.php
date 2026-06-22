<?php

namespace App\Actions;

use App\Exceptions\ForbiddenException;
use App\Services\PermissionService;
use Fig\Http\Message\StatusCodeInterface as HTTP;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class BAction {
    protected string $SESSION_PATH = '/';
    protected string $requiredPermission = '';

    public function __construct(protected readonly PermissionService $permission_service) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        if (!$this->requiredPermission)
            return $this->handle($request, $response, $args);

        // Check if the user has the required permission
        $role_id = $request->getAttribute('role_id');
        if (!$this->permission_service->hasPermissions($role_id, $this->requiredPermission))
            throw new ForbiddenException('You do not have permission to access this resource');

        return $this->handle($request, $response, $args);
    }

    protected abstract function handle(Request $request, Response $response, array $args): Response;

    protected function json(Response $response, mixed $data, int $status = HTTP::STATUS_OK): Response {
        $encoded = json_encode($data);

        $response->getBody()->write($encoded);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}