<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Infrastructure\Http;

use Gaby\Apps\Api\CAT\Domain\Exception\ProductNotFoundException;
use Gaby\Apps\Api\VEN\Application\CancelSale\CancelSaleCommand;
use Gaby\Apps\Api\VEN\Application\CancelSale\CancelSaleHandler;
use Gaby\Apps\Api\VEN\Application\CreateSale\CreateSaleCommand;
use Gaby\Apps\Api\VEN\Application\CreateSale\CreateSaleHandler;
use Gaby\Apps\Api\VEN\Application\GetSale\GetSaleHandler;
use Gaby\Apps\Api\VEN\Application\GetSale\GetSaleQuery;
use Gaby\Apps\Api\VEN\Application\ListSales\ListSalesHandler;
use Gaby\Apps\Api\VEN\Application\ListSales\ListSalesQuery;
use Gaby\Apps\Api\VEN\Domain\Exception\SaleAlreadyCancelledException;
use Gaby\Apps\Api\VEN\Domain\Exception\SaleCancellationNotAllowedException;
use Gaby\Apps\Api\VEN\Domain\Exception\SaleNotFoundException;
use Gaby\Apps\Api\VEN\Domain\Sale;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SaleController
{
    use JsonResponseTrait;

    /** @var ListSalesHandler */  private $listHandler;
    /** @var GetSaleHandler */    private $getHandler;
    /** @var CreateSaleHandler */ private $createHandler;
    /** @var CancelSaleHandler */ private $cancelHandler;

    public function __construct(
        ListSalesHandler $listHandler,
        GetSaleHandler $getHandler,
        CreateSaleHandler $createHandler,
        CancelSaleHandler $cancelHandler
    ) {
        $this->listHandler   = $listHandler;
        $this->getHandler    = $getHandler;
        $this->createHandler = $createHandler;
        $this->cancelHandler = $cancelHandler;
    }

    public function list(Request $request, Response $response): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $params   = $request->getQueryParams();
        $dateFrom = isset($params['dateFrom']) && $params['dateFrom'] !== '' ? (string)$params['dateFrom'] : null;
        $dateTo   = isset($params['dateTo'])   && $params['dateTo']   !== '' ? (string)$params['dateTo']   : null;
        $clientId = isset($params['clientId']) && $params['clientId'] !== '' ? (string)$params['clientId'] : null;
        return $this->json($response, $this->listHandler->handle(new ListSalesQuery($storeId, $dateFrom, $dateTo, $clientId)));
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $saleId  = (string) ($args['id'] ?? '');
        try {
            $data = $this->getHandler->handle(new GetSaleQuery($storeId, $saleId));
        } catch (SaleNotFoundException $e) {
            return $this->json($response, ['error' => 'Sale not found.'], 404);
        }
        return $this->json($response, $data);
    }

    public function create(Request $request, Response $response): Response
    {
        $storeId         = (string) $request->getAttribute('storeId');
        $responsibleId   = (string) $request->getAttribute('adminId');
        $responsibleName = $responsibleId;
        $data            = (array) $request->getParsedBody();
        $items         = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
        $paymentMethod = isset($data['paymentMethod']) ? trim((string)$data['paymentMethod']) : '';
        $clientId      = isset($data['clientId']) ? (trim((string)$data['clientId']) ?: null) : null;
        $notes         = isset($data['notes'])    ? (trim((string)$data['notes'])    ?: null) : null;

        if (empty($items)) {
            return $this->json($response, ['error' => 'items array is required and must not be empty.'], 422);
        }
        if (!in_array($paymentMethod, Sale::$validPaymentMethods, true)) {
            return $this->json($response, ['error' => 'paymentMethod must be one of: ' . implode(', ', Sale::$validPaymentMethods)], 422);
        }
        try {
            $sale = $this->createHandler->handle(
                new CreateSaleCommand($storeId, $clientId, $items, $paymentMethod, $notes, $responsibleId, $responsibleName)
            );
        } catch (ProductNotFoundException $e) {
            return $this->json($response, ['error' => 'Product not found.'], 404);
        }
        return $this->json($response, $sale->toArray(), 201);
    }

    public function cancel(Request $request, Response $response, array $args): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $role    = (string) $request->getAttribute('role');
        $saleId  = (string) ($args['id'] ?? '');
        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can cancel sales.'], 403);
        }
        $data        = (array) $request->getParsedBody();
        $reason      = isset($data['reason']) ? trim((string)$data['reason']) : '';
        $cancelledBy = (string) $request->getAttribute('adminId');
        if ($reason === '') {
            return $this->json($response, ['error' => 'reason is required.'], 422);
        }
        try {
            $this->cancelHandler->handle(
                new CancelSaleCommand($storeId, $saleId, $reason, $cancelledBy, date('Y-m-d'))
            );
        } catch (SaleNotFoundException $e) {
            return $this->json($response, ['error' => 'Sale not found.'], 404);
        } catch (SaleAlreadyCancelledException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 409);
        } catch (SaleCancellationNotAllowedException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 422);
        }
        return $response->withStatus(204);
    }
}
