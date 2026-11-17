<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Industry\Infrastructure\Http;

use Gaby\Apps\Api\Industry\Application\ListIndustries\ListIndustriesHandler;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class IndustryController
{
    use JsonResponseTrait;

    /** @var ListIndustriesHandler */
    private $handler;

    public function __construct(ListIndustriesHandler $handler)
    {
        $this->handler = $handler;
    }

    public function list(Request $request, Response $response): Response
    {
        return $this->json($response, $this->handler->handle());
    }
}
