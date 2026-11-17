<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Infrastructure\Http;

use Gaby\Apps\Api\Admin\Application\ListUsers\ListUsersQuery;
use Gaby\Apps\Api\Admin\Application\ListUsers\ListUsersHandler;
use Gaby\Apps\Api\Admin\Application\CreateUser\CreateUserCommand;
use Gaby\Apps\Api\Admin\Application\CreateUser\CreateUserHandler;
use Gaby\Apps\Api\Admin\Application\UpdateUser\UpdateUserCommand;
use Gaby\Apps\Api\Admin\Application\UpdateUser\UpdateUserHandler;
use Gaby\Apps\Api\Admin\Application\DeleteUser\DeleteUserCommand;
use Gaby\Apps\Api\Admin\Application\DeleteUser\DeleteUserHandler;
use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class UserController
{
    use JsonResponseTrait;

    /** @var ListUsersHandler */        private $listHandler;
    /** @var CreateUserHandler */       private $createHandler;
    /** @var UpdateUserHandler */       private $updateHandler;
    /** @var DeleteUserHandler */       private $deleteHandler;
    /** @var AdminRepositoryInterface */ private $adminRepo;

    public function __construct(
        ListUsersHandler $listHandler,
        CreateUserHandler $createHandler,
        UpdateUserHandler $updateHandler,
        DeleteUserHandler $deleteHandler,
        AdminRepositoryInterface $adminRepo
    ) {
        $this->listHandler   = $listHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
        $this->adminRepo     = $adminRepo;
    }

    public function list(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        return $this->json($response, $this->listHandler->handle(new ListUsersQuery($storeId)));
    }

    public function create(Request $request, Response $response): Response
    {
        if ($request->getAttribute('role') !== 'owner') {
            return $this->json($response, ['error' => 'Forbidden.'], 403);
        }

        $storeId = (string) $request->getAttribute('storeId');
        $body    = (array) $request->getParsedBody();
        $name     = isset($body['name'])     && $body['name']     !== '' ? (string) $body['name']     : null;
        $email    = isset($body['email'])    && $body['email']    !== '' ? (string) $body['email']    : null;
        $password = isset($body['password']) && $body['password'] !== '' ? (string) $body['password'] : null;
        $role     = isset($body['role'])     && $body['role']     !== '' ? (string) $body['role']     : 'vendor';

        if ($name === null || $email === null || $password === null) {
            return $this->json($response, ['error' => 'name, email and password are required.'], 422);
        }

        try {
            $data = $this->createHandler->handle(new CreateUserCommand($storeId, $name, $email, $password, $role));
            return $this->json($response, $data, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 422);
        } catch (\DomainException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 409);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        if ($request->getAttribute('role') !== 'owner') {
            return $this->json($response, ['error' => 'Forbidden.'], 403);
        }

        $storeId = (string) $request->getAttribute('storeId');
        $userId  = (string) ($args['id'] ?? '');
        $body    = (array) $request->getParsedBody();
        $name    = isset($body['name'])   && $body['name']   !== '' ? (string) $body['name']   : null;
        $status  = isset($body['status']) && $body['status'] !== '' ? (string) $body['status'] : null;

        try {
            $data = $this->updateHandler->handle(new UpdateUserCommand($storeId, $userId, $name, $status));
            return $this->json($response, $data);
        } catch (\DomainException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 404);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if ($request->getAttribute('role') !== 'owner') {
            return $this->json($response, ['error' => 'Forbidden.'], 403);
        }

        $storeId = (string) $request->getAttribute('storeId');
        $userId  = (string) ($args['id'] ?? '');

        // Prevent self-deletion
        if ($userId === (string) $request->getAttribute('adminId')) {
            return $this->json($response, ['error' => 'Cannot deactivate your own account.'], 422);
        }

        try {
            $this->deleteHandler->handle(new DeleteUserCommand($storeId, $userId));
            return $response->withStatus(204);
        } catch (\DomainException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 404);
        }
    }

    public function deleteMe(Request $request, Response $response): Response
    {
        $adminId = (string) $request->getAttribute('adminId');
        $this->adminRepo->deleteById($adminId);
        return $response->withStatus(204);
    }
}
