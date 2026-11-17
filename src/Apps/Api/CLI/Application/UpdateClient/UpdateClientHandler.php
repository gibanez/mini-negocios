<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Application\UpdateClient;

use Gaby\Apps\Api\CLI\Domain\Exception\ClientNotFoundException;
use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;

final class UpdateClientHandler
{
    /** @var ClientRepositoryInterface */ private $repo;

    public function __construct(ClientRepositoryInterface $repo) { $this->repo = $repo; }

    public function handle(UpdateClientCommand $cmd): void
    {
        $client = $this->repo->findById($cmd->clientId);
        if ($client === null || $client->storeId() !== $cmd->storeId) {
            throw ClientNotFoundException::withId($cmd->clientId);
        }
        $client->update($cmd->name, $cmd->nickname, $cmd->phone, $cmd->address, $cmd->notes);
        $this->repo->save($client);
    }
}
