<?php

use App\Exceptions\ApiException;
use App\Middlewares\CorsMiddleware;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/dependencies.php');
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$debug = (bool)($_ENV['API_DEBUG'] ?? false);
$errorMiddleware = $app->addErrorMiddleware($debug, true, true);

$app->add(new CorsMiddleware($app->getResponseFactory()));

$errorMiddleware->setDefaultErrorHandler(
    function (
        Psr\Http\Message\ServerRequestInterface $request,
        Throwable                               $exception,
        bool                                    $displayErrorDetails
    ) use ($app) {
        $statusCode = $exception instanceof ApiException ? $exception->getStatusCode() : 500;
        $message = $exception instanceof ApiException ? $exception->getMessage() : 'Something went wrong';

        $payload = ['error' => $message];

        // Only leak internals (e.g. raw exception message) when APP_DEBUG is on
        if ($displayErrorDetails && $statusCode === 500) {
            $payload['exception'] = $exception->getMessage();
        }

        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(json_encode($payload));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
);

(require __DIR__ . '/../config/routes.php')($app);

$app->run();