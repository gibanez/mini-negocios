<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\GetPublicSummary;
final class GetPublicSummaryQuery
{
    /** @var string */ public $token;
    public function __construct(string $token) { $this->token = $token; }
}
