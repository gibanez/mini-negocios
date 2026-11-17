<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\ListAccounts;
final class ListAccountsQuery
{
    /** @var string */ public $storeId;
    public function __construct(string $storeId) { $this->storeId = $storeId; }
}
