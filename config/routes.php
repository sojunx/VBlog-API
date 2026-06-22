<?php

use App\Actions\Users\AuthenticateUserAction;
use App\Actions\Users\CreateUserAction;
use App\Actions\Users\ListUsersAction;
use App\Actions\Users\LogoutUserAction;
use App\Actions\Users\RegrantUserAccessAction;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/api/v1', function (RouteCollectorProxy $version) {

        // /api/v1/users
        $version->group('/users', function (RouteCollectorProxy $route) {
            $route->post('', CreateUserAction::class);
            $route->post('/authenticate', AuthenticateUserAction::class);
            $route->get('/refresh', RegrantUserAccessAction::class);

            $route->get('', ListUsersAction::class)->add(AuthMiddleware::class);
            $route->delete('/logout', LogoutUserAction::class)->add(AuthMiddleware::class);
        });
    });
};
