<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Infrastructure\Http;

use Gaby\Apps\Api\ACC\Domain\Exception\EmailAlreadyInUseException;
use Gaby\Apps\Api\PLA\Application\ActivateStore\ActivateStoreCommand;
use Gaby\Apps\Api\PLA\Application\ActivateStore\ActivateStoreHandler;
use Gaby\Apps\Api\PLA\Application\CreateStoreAdmin\CreateStoreAdminCommand;
use Gaby\Apps\Api\PLA\Application\CreateStoreAdmin\CreateStoreAdminHandler;
use Gaby\Apps\Api\PLA\Application\DeactivateAdmin\DeactivateAdminCommand;
use Gaby\Apps\Api\PLA\Application\DeactivateAdmin\DeactivateAdminHandler;
use Gaby\Apps\Api\PLA\Application\GetStats\GetStatsHandler;
use Gaby\Apps\Api\PLA\Application\GetStats\GetStatsQuery;
use Gaby\Apps\Api\PLA\Application\GetStore\GetStoreHandler;
use Gaby\Apps\Api\PLA\Application\GetStore\GetStoreQuery;
use Gaby\Apps\Api\PLA\Application\ListStoreAdmins\ListStoreAdminsHandler;
use Gaby\Apps\Api\PLA\Application\ListStoreAdmins\ListStoreAdminsQuery;
use Gaby\Apps\Api\PLA\Application\ListStores\ListStoresHandler;
use Gaby\Apps\Api\PLA\Application\ListStores\ListStoresQuery;
use Gaby\Apps\Api\PLA\Application\SuspendStore\SuspendStoreCommand;
use Gaby\Apps\Api\PLA\Application\SuspendStore\SuspendStoreHandler;
use Gaby\Apps\Api\PLA\Application\UpdateStore\UpdateStoreCommand;
use Gaby\Apps\Api\PLA\Application\UpdateStore\UpdateStoreHandler;
use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PlatformController
{
    use JsonResponseTrait;

    /** @var ListStoresHandler */
    private $listHandler;

    /** @var GetStoreHandler */
    private $getHandler;

    /** @var SuspendStoreHandler */
    private $suspendHandler;

    /** @var ActivateStoreHandler */
    private $activateHandler;

    /** @var GetStatsHandler */
    private $statsHandler;

    /** @var UpdateStoreHandler */
    private $updateStoreHandler;

    /** @var ListStoreAdminsHandler */
    private $listAdminsHandler;

    /** @var CreateStoreAdminHandler */
    private $createAdminHandler;

    /** @var DeactivateAdminHandler */
    private $deactivateAdminHandler;

    public function __construct(
        ListStoresHandler $listHandler,
        GetStoreHandler $getHandler,
        SuspendStoreHandler $suspendHandler,
        ActivateStoreHandler $activateHandler,
        GetStatsHandler $statsHandler,
        UpdateStoreHandler $updateStoreHandler,
        ListStoreAdminsHandler $listAdminsHandler,
        CreateStoreAdminHandler $createAdminHandler,
        DeactivateAdminHandler $deactivateAdminHandler
    ) {
        $this->listHandler            = $listHandler;
        $this->getHandler             = $getHandler;
        $this->suspendHandler         = $suspendHandler;
        $this->activateHandler        = $activateHandler;
        $this->statsHandler           = $statsHandler;
        $this->updateStoreHandler     = $updateStoreHandler;
        $this->listAdminsHandler      = $listAdminsHandler;
        $this->createAdminHandler     = $createAdminHandler;
        $this->deactivateAdminHandler = $deactivateAdminHandler;
    }

    public function listStores(Request $request, Response $response): Response
    {
        return $this->json($response, $this->listHandler->handle(new ListStoresQuery()));
    }

    public function getStore(Request $request, Response $response, array $args): Response
    {
        $storeId = (string) ($args['id'] ?? '');
        try {
            $data = $this->getHandler->handle(new GetStoreQuery($storeId));
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => 'Store not found.'], 404);
        }
        return $this->json($response, $data);
    }

    public function updateStore(Request $request, Response $response, array $args): Response
    {
        $storeId = (string) ($args['id'] ?? '');
        $data    = (array) $request->getParsedBody();
        $name    = isset($data['name']) ? trim((string) $data['name']) : '';
        if ($name === '') {
            return $this->json($response, ['error' => 'name is required.'], 422);
        }
        try {
            $store = $this->updateStoreHandler->handle(new UpdateStoreCommand($storeId, $name));
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => 'Store not found.'], 404);
        }
        return $this->json($response, $store);
    }

    public function suspendStore(Request $request, Response $response, array $args): Response
    {
        $storeId = (string) ($args['id'] ?? '');
        $data    = (array) $request->getParsedBody();
        $reason  = isset($data['reason']) ? trim((string) $data['reason']) : '';
        if ($reason === '') {
            return $this->json($response, ['error' => 'reason is required.'], 422);
        }
        try {
            $this->suspendHandler->handle(new SuspendStoreCommand($storeId, $reason));
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => 'Store not found.'], 404);
        }
        return $this->json($response, ['suspended' => true]);
    }

    public function activateStore(Request $request, Response $response, array $args): Response
    {
        $storeId = (string) ($args['id'] ?? '');
        try {
            $this->activateHandler->handle(new ActivateStoreCommand($storeId));
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => 'Store not found.'], 404);
        }
        return $this->json($response, ['activated' => true]);
    }

    public function listStoreAdmins(Request $request, Response $response, array $args): Response
    {
        $storeId = (string) ($args['id'] ?? '');
        return $this->json($response, $this->listAdminsHandler->handle(new ListStoreAdminsQuery($storeId)));
    }

    public function createStoreAdmin(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) ($args['id'] ?? '');
        $data     = (array) $request->getParsedBody();
        $name     = isset($data['name'])     ? trim((string) $data['name'])  : '';
        $email    = isset($data['email'])    ? trim((string) $data['email']) : '';
        $password = isset($data['password']) ? (string) $data['password']    : '';
        $role     = isset($data['role'])     ? trim((string) $data['role'])  : 'vendor';

        if ($name === '' || $email === '' || $password === '') {
            return $this->json($response, ['error' => 'name, email and password are required.'], 422);
        }

        try {
            $admin = $this->createAdminHandler->handle(
                new CreateStoreAdminCommand($storeId, $name, $email, $password, $role)
            );
        } catch (EmailAlreadyInUseException $e) {
            return $this->json($response, ['error' => 'Email already in use.'], 409);
        }

        return $this->json($response, $admin, 201);
    }

    public function deactivateAdmin(Request $request, Response $response, array $args): Response
    {
        $adminId = (string) ($args['adminId'] ?? '');
        $this->deactivateAdminHandler->handle(new DeactivateAdminCommand($adminId));
        return $response->withStatus(204);
    }

    public function stats(Request $request, Response $response): Response
    {
        return $this->json($response, $this->statsHandler->handle(new GetStatsQuery()));
    }
}
