<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use function in_array;

readonly class CorsMiddleware implements MiddlewareInterface {
    public function __construct(private ResponseFactoryInterface $factory) {}

    public function process(Request $request, Handler $handler): Response {
        $origin = $request->getHeaderLine('Origin');
        $allowed_origins = ['http://localhost:3000'];

        if ($request->getMethod() === 'OPTIONS')
            $response = $this->factory->createResponse();
        else
            $response = $handler->handle($request);

        if (in_array($origin, $allowed_origins, true))
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Credentials', 'true');

        return $response
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Vary', 'Origin');
    }
}