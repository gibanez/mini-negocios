<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Infrastructure\Http;

use Gaby\Apps\Api\CLI\Application\CreateClient\CreateClientCommand;
use Gaby\Apps\Api\CLI\Application\CreateClient\CreateClientHandler;
use Gaby\Apps\Api\CLI\Application\DeactivateClient\DeactivateClientCommand;
use Gaby\Apps\Api\CLI\Application\DeactivateClient\DeactivateClientHandler;
use Gaby\Apps\Api\CLI\Application\ListClients\ListClientsHandler;
use Gaby\Apps\Api\CLI\Application\ListClients\ListClientsQuery;
use Gaby\Apps\Api\CLI\Application\UpdateClient\UpdateClientCommand;
use Gaby\Apps\Api\CLI\Application\UpdateClient\UpdateClientHandler;
use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;
use Gaby\Apps\Api\CLI\Domain\Exception\ClientNotFoundException;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ClientController
{
    use JsonResponseTrait;

    /** @var ListClientsHandler */      private $listHandler;
    /** @var CreateClientHandler */     private $createHandler;
    /** @var UpdateClientHandler */     private $updateHandler;
    /** @var DeactivateClientHandler */ private $deactivateHandler;
    /** @var ClientRepositoryInterface */ private $repo;

    public function __construct(
        ListClientsHandler $listHandler,
        CreateClientHandler $createHandler,
        UpdateClientHandler $updateHandler,
        DeactivateClientHandler $deactivateHandler,
        ClientRepositoryInterface $repo
    ) {
        $this->listHandler       = $listHandler;
        $this->createHandler     = $createHandler;
        $this->updateHandler     = $updateHandler;
        $this->deactivateHandler = $deactivateHandler;
        $this->repo              = $repo;
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $clientId = (string) ($args['id'] ?? '');
        $client   = $this->repo->findById($clientId);
        if ($client === null || $client->storeId() !== $storeId) {
            return $this->json($response, ['error' => 'Client not found.'], 404);
        }
        return $this->json($response, $client->toArray());
    }

    public function list(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $q       = $request->getQueryParams()['q'] ?? null;
        return $this->json($response, $this->listHandler->handle(new ListClientsQuery($storeId, $q)));
    }

    public function create(Request $request, Response $response): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $data     = (array) $request->getParsedBody();
        $name     = isset($data['name'])     ? trim((string)$data['name'])     : '';
        $nickname = isset($data['nickname']) ? (trim((string)$data['nickname']) ?: null) : null;
        $phone    = isset($data['phone'])    ? (trim((string)$data['phone'])    ?: null) : null;
        $address  = isset($data['address'])  ? (trim((string)$data['address'])  ?: null) : null;
        $notes    = isset($data['notes'])    ? (trim((string)$data['notes'])    ?: null) : null;

        if ($name === '') {
            return $this->json($response, ['error' => 'name is required.'], 422);
        }
        $client = $this->createHandler->handle(
            new CreateClientCommand($storeId, $name, $nickname, $phone, $address, $notes)
        );
        return $this->json($response, $client->toArray(), 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $clientId = (string) ($args['id'] ?? '');
        $data     = (array) $request->getParsedBody();
        $name     = isset($data['name'])     ? trim((string)$data['name'])     : '';
        $nickname = isset($data['nickname']) ? (trim((string)$data['nickname']) ?: null) : null;
        $phone    = isset($data['phone'])    ? (trim((string)$data['phone'])    ?: null) : null;
        $address  = isset($data['address'])  ? (trim((string)$data['address'])  ?: null) : null;
        $notes    = isset($data['notes'])    ? (trim((string)$data['notes'])    ?: null) : null;

        if ($name === '') {
            return $this->json($response, ['error' => 'name is required.'], 422);
        }
        try {
            $this->updateHandler->handle(
                new UpdateClientCommand($storeId, $clientId, $name, $nickname, $phone, $address, $notes)
            );
        } catch (ClientNotFoundException $e) {
            return $this->json($response, ['error' => 'Client not found.'], 404);
        }
        return $this->json($response, ['id' => $clientId, 'name' => $name]);
    }

    public function deactivate(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $clientId = (string) ($args['id'] ?? '');
        try {
            $this->deactivateHandler->handle(new DeactivateClientCommand($storeId, $clientId));
        } catch (ClientNotFoundException $e) {
            return $this->json($response, ['error' => 'Client not found.'], 404);
        }
        return $response->withStatus(204);
    }
}
