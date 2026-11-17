<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Infrastructure\Http;

use Gaby\Apps\Api\FIA\Application\AddDebt\AddDebtCommand;
use Gaby\Apps\Api\FIA\Application\AddDebt\AddDebtHandler;
use Gaby\Apps\Api\FIA\Application\AddPayment\AddPaymentCommand;
use Gaby\Apps\Api\FIA\Application\AddPayment\AddPaymentHandler;
use Gaby\Apps\Api\FIA\Application\CancelPayment\CancelPaymentCommand;
use Gaby\Apps\Api\FIA\Application\CancelPayment\CancelPaymentHandler;
use Gaby\Apps\Api\FIA\Application\GetAccount\GetAccountHandler;
use Gaby\Apps\Api\FIA\Application\GetAccount\GetAccountQuery;
use Gaby\Apps\Api\FIA\Application\GetPublicSummary\GetPublicSummaryHandler;
use Gaby\Apps\Api\FIA\Application\GetPublicSummary\GetPublicSummaryQuery;
use Gaby\Apps\Api\FIA\Application\ListAccounts\ListAccountsHandler;
use Gaby\Apps\Api\FIA\Application\ListAccounts\ListAccountsQuery;
use Gaby\Apps\Api\FIA\Application\RenewToken\RenewTokenCommand;
use Gaby\Apps\Api\FIA\Application\RenewToken\RenewTokenHandler;
use Gaby\Apps\Api\FIA\Domain\Exception\CreditAccountNotFoundException;
use Gaby\Apps\Api\FIA\Domain\Exception\MovementAlreadyCancelledException;
use Gaby\Apps\Api\FIA\Domain\Exception\MovementNotFoundException;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class FiadoController
{
    use JsonResponseTrait;

    /** @var ListAccountsHandler */     private $listHandler;
    /** @var GetAccountHandler */       private $getHandler;
    /** @var AddDebtHandler */          private $addDebtHandler;
    /** @var AddPaymentHandler */       private $addPaymentHandler;
    /** @var CancelPaymentHandler */    private $cancelPaymentHandler;
    /** @var RenewTokenHandler */       private $renewTokenHandler;
    /** @var GetPublicSummaryHandler */ private $publicSummaryHandler;

    public function __construct(
        ListAccountsHandler $listHandler,
        GetAccountHandler $getHandler,
        AddDebtHandler $addDebtHandler,
        AddPaymentHandler $addPaymentHandler,
        CancelPaymentHandler $cancelPaymentHandler,
        RenewTokenHandler $renewTokenHandler,
        GetPublicSummaryHandler $publicSummaryHandler
    ) {
        $this->listHandler          = $listHandler;
        $this->getHandler           = $getHandler;
        $this->addDebtHandler       = $addDebtHandler;
        $this->addPaymentHandler    = $addPaymentHandler;
        $this->cancelPaymentHandler = $cancelPaymentHandler;
        $this->renewTokenHandler    = $renewTokenHandler;
        $this->publicSummaryHandler = $publicSummaryHandler;
    }

    public function list(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        return $this->json($response, $this->listHandler->handle(new ListAccountsQuery($storeId)));
    }

    public function getAccount(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $clientId = (string) ($args['clientId'] ?? '');
        return $this->json($response, $this->getHandler->handle(new GetAccountQuery($storeId, $clientId)));
    }

    public function addDebt(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $clientId = (string) ($args['clientId'] ?? '');
        $data     = (array) $request->getParsedBody();
        $amount   = isset($data['amount']) ? (int)$data['amount'] : 0;
        $saleId   = isset($data['saleId']) ? (trim((string)$data['saleId']) ?: null) : null;
        $notes    = isset($data['notes'])  ? (trim((string)$data['notes'])  ?: null) : null;

        if ($amount <= 0) {
            return $this->json($response, ['error' => 'amount must be greater than zero.'], 422);
        }
        $movement = $this->addDebtHandler->handle(new AddDebtCommand($storeId, $clientId, $amount, $saleId, $notes));
        return $this->json($response, $movement->toArray(), 201);
    }

    public function addPayment(Request $request, Response $response, array $args): Response
    {
        $storeId       = (string) $request->getAttribute('storeId');
        $clientId      = (string) ($args['clientId'] ?? '');
        $data          = (array) $request->getParsedBody();
        $amount        = isset($data['amount'])        ? (int)$data['amount']                  : 0;
        $paymentMethod = isset($data['paymentMethod']) ? trim((string)$data['paymentMethod'])  : '';
        $notes         = isset($data['notes'])         ? (trim((string)$data['notes']) ?: null) : null;

        if ($amount <= 0) {
            return $this->json($response, ['error' => 'amount must be greater than zero.'], 422);
        }
        $validMethods = ['efectivo', 'transferencia', 'debito', 'credito'];
        if (!in_array($paymentMethod, $validMethods, true)) {
            return $this->json($response, ['error' => 'paymentMethod must be one of: ' . implode(', ', $validMethods)], 422);
        }
        $movement = $this->addPaymentHandler->handle(new AddPaymentCommand($storeId, $clientId, $amount, $paymentMethod, $notes));
        return $this->json($response, $movement->toArray(), 201);
    }

    public function cancelPayment(Request $request, Response $response, array $args): Response
    {
        $storeId    = (string) $request->getAttribute('storeId');
        $role       = (string) $request->getAttribute('role');
        $movementId = (string) ($args['movementId'] ?? '');
        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can cancel payments.'], 403);
        }
        $data        = (array) $request->getParsedBody();
        $reason      = isset($data['reason']) ? trim((string)$data['reason']) : '';
        $cancelledBy = (string) $request->getAttribute('adminId');
        if ($reason === '') {
            return $this->json($response, ['error' => 'reason is required.'], 422);
        }
        try {
            $this->cancelPaymentHandler->handle(new CancelPaymentCommand($storeId, $movementId, $reason, $cancelledBy));
        } catch (MovementNotFoundException $e) {
            return $this->json($response, ['error' => 'Movement not found.'], 404);
        } catch (MovementAlreadyCancelledException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 409);
        }
        return $response->withStatus(204);
    }

    public function renewToken(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $role     = (string) $request->getAttribute('role');
        $clientId = (string) ($args['clientId'] ?? '');
        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can renew public links.'], 403);
        }
        $token = $this->renewTokenHandler->handle(new RenewTokenCommand($storeId, $clientId));
        return $this->json($response, ['publicToken' => $token]);
    }

    public function publicSummary(Request $request, Response $response, array $args): Response
    {
        $token = (string) ($args['token'] ?? '');
        try {
            $data = $this->publicSummaryHandler->handle(new GetPublicSummaryQuery($token));
        } catch (CreditAccountNotFoundException $e) {
            return $this->json($response, ['error' => 'Account not found.'], 404);
        }
        return $this->json($response, $data);
    }
}
