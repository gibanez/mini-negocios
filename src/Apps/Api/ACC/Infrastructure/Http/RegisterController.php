<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ACC\Infrastructure\Http;

use Gaby\Apps\Api\ACC\Application\Register\RegisterCommand;
use Gaby\Apps\Api\ACC\Application\Register\RegisterHandler;
use Gaby\Apps\Api\ACC\Domain\Exception\EmailAlreadyInUseException;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RegisterController
{
    use JsonResponseTrait;

    /** @var RegisterHandler */
    private $handler;

    public function __construct(RegisterHandler $handler)
    {
        $this->handler = $handler;
    }

    public function register(Request $request, Response $response): Response
    {
        $data      = (array) $request->getParsedBody();
        $storeName = isset($data['storeName']) ? trim((string) $data['storeName']) : '';
        $ownerName = isset($data['ownerName']) ? trim((string) $data['ownerName']) : '';
        $email     = isset($data['email'])     ? trim((string) $data['email'])     : '';
        $password  = isset($data['password'])  ? (string) $data['password']        : '';

        if ($storeName === '' || $ownerName === '' || $email === '' || $password === '') {
            return $this->json($response, ['error' => 'storeName, ownerName, email and password are required.'], 422);
        }

        if (strlen($password) < 8) {
            return $this->json($response, ['error' => 'Password must be at least 8 characters.'], 422);
        }

        try {
            $result = $this->handler->handle(new RegisterCommand($storeName, $ownerName, $email, $password));
        } catch (EmailAlreadyInUseException $e) {
            return $this->json($response, ['error' => 'Email already in use.'], 409);
        }

        return $this->json($response, [
            'token' => $result->token,
            'user'  => $result->user,
        ], 201);
    }
}
