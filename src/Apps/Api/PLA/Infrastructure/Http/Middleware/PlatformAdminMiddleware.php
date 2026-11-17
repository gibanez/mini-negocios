<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Infrastructure\Http\Middleware;

use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PlatformAdminMiddleware implements MiddlewareInterface
{
    use JsonResponseTrait;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $role = (string) $request->getAttribute('role', '');
        if ($role !== 'platform_admin') {
            $response = $this->responseFactory->createResponse();
            return $this->json($response, ['error' => 'Platform admin access required.'], 403);
        }
        return $handler->handle($request);
    }
}
