<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Application\CreateClient;

use Gaby\Apps\Api\CLI\Domain\Client;
use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;

final class CreateClientHandler
{
    /** @var ClientRepositoryInterface */ private $repo;

    public function __construct(ClientRepositoryInterface $repo) { $this->repo = $repo; }

    public function handle(CreateClientCommand $cmd): Client
    {
        $client = Client::create($cmd->storeId, $cmd->name, $cmd->nickname, $cmd->phone, $cmd->address, $cmd->notes);
        $this->repo->save($client);
        return $client;
    }
}
