<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\RenewToken;
final class RenewTokenCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $clientId;
    public function __construct(string $storeId, string $clientId) { $this->storeId=$storeId; $this->clientId=$clientId; }
}
