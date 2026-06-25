<?php

use App\Actions\Posts\GetPostAction;
use App\Actions\Posts\ListPostsAction;
use App\Actions\Posts\UpdatePostAction;
use App\Actions\Users\AuthenticateUserAction;
use App\Actions\Users\CreateUserAction;
use App\Actions\Users\GetUserAction;
use App\Actions\Users\ListUsersAction;
use App\Actions\Users\LogoutUserAction;
use App\Actions\Users\RegrantUserAccessAction;
use App\Actions\Users\UpdateUserProfileAction;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/api/v1', function (RouteCollectorProxy $version) {

        // ROUTES: /api/v1/users
        $version->group('/users', function (RouteCollectorProxy $route) {
            $route->post('', CreateUserAction::class);
            $route->post('/authenticate', AuthenticateUserAction::class);
            $route->get('/refresh', RegrantUserAccessAction::class);

            $route->get('/me', GetUserAction::class)->add(AuthMiddleware::class);
            $route->get('', ListUsersAction::class)->add(AuthMiddleware::class);
            $route->delete('/logout', LogoutUserAction::class)->add(AuthMiddleware::class);
            $route->post('/update', UpdateUserProfileAction::class)->add(AuthMiddleware::class);
        });

        // ROUTES: /api/v1/posts
        $version->group('/posts', function (RouteCollectorProxy $route) {
            $route->get('', ListPostsAction::class);
            $route->get('/{id}', GetPostAction::class);

            $route->put('/{id}', UpdatePostAction::class)->add(AuthMiddleware::class);
        });
    });
};
