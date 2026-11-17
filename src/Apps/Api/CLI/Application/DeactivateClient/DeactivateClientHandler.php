<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Application\DeactivateClient;

use Gaby\Apps\Api\CLI\Domain\Exception\ClientNotFoundException;
use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;

final class DeactivateClientHandler
{
    /** @var ClientRepositoryInterface */ private $repo;

    public function __construct(ClientRepositoryInterface $repo) { $this->repo = $repo; }

    public function handle(DeactivateClientCommand $cmd): void
    {
        $client = $this->repo->findById($cmd->clientId);
        if ($client === null || $client->storeId() !== $cmd->storeId) {
            throw ClientNotFoundException::withId($cmd->clientId);
        }
        $client->deactivate();
        $this->repo->save($client);
    }
}
