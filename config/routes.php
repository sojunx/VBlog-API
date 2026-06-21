<?php

use App\Actions\Users\AuthenticateUserAction;
use App\Actions\Users\CreateUserAction;
use App\Actions\Users\ListUsersAction;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/api/v1', function (RouteCollectorProxy $version) {

        // /api/v1/users
        $version->group('/users', function (RouteCollectorProxy $route) {
            $route->post('', CreateUserAction::class);
            $route->post('/authenticate', AuthenticateUserAction::class);

            $route->get('', ListUsersAction::class)->add(AuthMiddleware::class);
        });
    });
};
