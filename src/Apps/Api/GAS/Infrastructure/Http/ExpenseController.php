<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Infrastructure\Http;

use Gaby\Apps\Api\GAS\Application\CreateExpense\CreateExpenseCommand;
use Gaby\Apps\Api\GAS\Application\CreateExpense\CreateExpenseHandler;
use Gaby\Apps\Api\GAS\Application\DeleteExpense\DeleteExpenseCommand;
use Gaby\Apps\Api\GAS\Application\DeleteExpense\DeleteExpenseHandler;
use Gaby\Apps\Api\GAS\Application\GetExpense\GetExpenseHandler;
use Gaby\Apps\Api\GAS\Application\GetExpense\GetExpenseQuery;
use Gaby\Apps\Api\GAS\Application\ListExpenses\ListExpensesHandler;
use Gaby\Apps\Api\GAS\Application\ListExpenses\ListExpensesQuery;
use Gaby\Apps\Api\GAS\Application\UpdateExpense\UpdateExpenseCommand;
use Gaby\Apps\Api\GAS\Application\UpdateExpense\UpdateExpenseHandler;
use Gaby\Apps\Api\GAS\Domain\Exception\ExpenseNotFoundException;
use Gaby\Apps\Api\GAS\Domain\Expense;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ExpenseController
{
    use JsonResponseTrait;

    /** @var ListExpensesHandler */  private $listHandler;
    /** @var GetExpenseHandler */    private $getHandler;
    /** @var CreateExpenseHandler */ private $createHandler;
    /** @var UpdateExpenseHandler */ private $updateHandler;
    /** @var DeleteExpenseHandler */ private $deleteHandler;

    public function __construct(
        ListExpensesHandler $listHandler,
        GetExpenseHandler $getHandler,
        CreateExpenseHandler $createHandler,
        UpdateExpenseHandler $updateHandler,
        DeleteExpenseHandler $deleteHandler
    ) {
        $this->listHandler   = $listHandler;
        $this->getHandler    = $getHandler;
        $this->createHandler = $createHandler;
        $this->updateHandler = $updateHandler;
        $this->deleteHandler = $deleteHandler;
    }

    public function list(Request $request, Response $response): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $params   = $request->getQueryParams();
        $dateFrom = isset($params['dateFrom']) && $params['dateFrom'] !== '' ? (string)$params['dateFrom'] : null;
        $dateTo   = isset($params['dateTo'])   && $params['dateTo']   !== '' ? (string)$params['dateTo']   : null;
        $category = isset($params['category']) && $params['category'] !== '' ? (string)$params['category'] : null;
        return $this->json($response, $this->listHandler->handle(new ListExpensesQuery($storeId, $dateFrom, $dateTo, $category)));
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $storeId   = (string) $request->getAttribute('storeId');
        $expenseId = (string) ($args['id'] ?? '');
        try {
            $data = $this->getHandler->handle(new GetExpenseQuery($storeId, $expenseId));
        } catch (ExpenseNotFoundException $e) {
            return $this->json($response, ['error' => 'Expense not found.'], 404);
        }
        return $this->json($response, $data);
    }

    public function create(Request $request, Response $response): Response
    {
        $storeId         = (string) $request->getAttribute('storeId');
        $responsibleId   = (string) $request->getAttribute('adminId');
        $responsibleName = $responsibleId;
        $data            = (array) $request->getParsedBody();

        $amount        = isset($data['amount'])        ? (int)$data['amount']                      : 0;
        $category      = isset($data['category'])      ? trim((string)$data['category'])           : '';
        $recipient     = isset($data['recipient'])     ? trim((string)$data['recipient'])          : '';
        $description   = isset($data['description'])   ? (trim((string)$data['description']) ?: null) : null;
        $paymentMethod = isset($data['paymentMethod']) ? trim((string)$data['paymentMethod'])      : '';
        $date          = isset($data['date'])          ? trim((string)$data['date'])               : '';

        if ($amount <= 0) {
            return $this->json($response, ['error' => 'amount must be a positive integer (cents).'], 422);
        }
        if (!in_array($category, Expense::$validCategories, true)) {
            return $this->json($response, ['error' => 'category must be one of: ' . implode(', ', Expense::$validCategories)], 422);
        }
        if ($recipient === '') {
            return $this->json($response, ['error' => 'recipient is required.'], 422);
        }
        if (!in_array($paymentMethod, Expense::$validPaymentMethods, true)) {
            return $this->json($response, ['error' => 'paymentMethod must be one of: ' . implode(', ', Expense::$validPaymentMethods)], 422);
        }
        if ($date === '') {
            return $this->json($response, ['error' => 'date is required (Y-m-d).'], 422);
        }

        $expense = $this->createHandler->handle(
            new CreateExpenseCommand($storeId, $amount, $category, $recipient, $description,
                $paymentMethod, $responsibleId, $responsibleName, $date)
        );
        return $this->json($response, $expense->toArray(), 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $storeId       = (string) $request->getAttribute('storeId');
        $expenseId     = (string) ($args['id'] ?? '');
        $data          = (array) $request->getParsedBody();

        $amount        = isset($data['amount'])        ? (int)$data['amount']                      : 0;
        $category      = isset($data['category'])      ? trim((string)$data['category'])           : '';
        $recipient     = isset($data['recipient'])     ? trim((string)$data['recipient'])          : '';
        $description   = isset($data['description'])   ? (trim((string)$data['description']) ?: null) : null;
        $paymentMethod = isset($data['paymentMethod']) ? trim((string)$data['paymentMethod'])      : '';
        $date          = isset($data['date'])          ? trim((string)$data['date'])               : '';

        if ($amount <= 0) {
            return $this->json($response, ['error' => 'amount must be a positive integer (cents).'], 422);
        }
        if (!in_array($category, Expense::$validCategories, true)) {
            return $this->json($response, ['error' => 'category must be one of: ' . implode(', ', Expense::$validCategories)], 422);
        }
        if ($recipient === '') {
            return $this->json($response, ['error' => 'recipient is required.'], 422);
        }
        if (!in_array($paymentMethod, Expense::$validPaymentMethods, true)) {
            return $this->json($response, ['error' => 'paymentMethod must be one of: ' . implode(', ', Expense::$validPaymentMethods)], 422);
        }
        if ($date === '') {
            return $this->json($response, ['error' => 'date is required (Y-m-d).'], 422);
        }

        try {
            $this->updateHandler->handle(
                new UpdateExpenseCommand($storeId, $expenseId, $amount, $category, $recipient,
                    $description, $paymentMethod, $date)
            );
        } catch (ExpenseNotFoundException $e) {
            return $this->json($response, ['error' => 'Expense not found.'], 404);
        }
        return $response->withStatus(204);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $storeId   = (string) $request->getAttribute('storeId');
        $expenseId = (string) ($args['id'] ?? '');
        try {
            $this->deleteHandler->handle(new DeleteExpenseCommand($storeId, $expenseId));
        } catch (ExpenseNotFoundException $e) {
            return $this->json($response, ['error' => 'Expense not found.'], 404);
        }
        return $response->withStatus(204);
    }
}
