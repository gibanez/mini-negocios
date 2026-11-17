<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Infrastructure\Http;

use Gaby\Apps\Api\CAT\Application\AdjustStock\AdjustStockCommand;
use Gaby\Apps\Api\CAT\Application\AdjustStock\AdjustStockHandler;
use Gaby\Apps\Api\CAT\Application\CreateCategory\CreateCategoryCommand;
use Gaby\Apps\Api\CAT\Application\CreateCategory\CreateCategoryHandler;
use Gaby\Apps\Api\CAT\Application\CreateProduct\CreateProductCommand;
use Gaby\Apps\Api\CAT\Application\CreateProduct\CreateProductHandler;
use Gaby\Apps\Api\CAT\Application\DeactivateCategory\DeactivateCategoryCommand;
use Gaby\Apps\Api\CAT\Application\DeactivateCategory\DeactivateCategoryHandler;
use Gaby\Apps\Api\CAT\Application\DeactivateProduct\DeactivateProductCommand;
use Gaby\Apps\Api\CAT\Application\DeactivateProduct\DeactivateProductHandler;
use Gaby\Apps\Api\CAT\Application\GetStockHistory\GetStockHistoryHandler;
use Gaby\Apps\Api\CAT\Application\GetStockHistory\GetStockHistoryQuery;
use Gaby\Apps\Api\CAT\Application\ListCatalog\ListCatalogHandler;
use Gaby\Apps\Api\CAT\Application\ListCatalog\ListCatalogQuery;
use Gaby\Apps\Api\CAT\Application\SearchProducts\SearchProductsHandler;
use Gaby\Apps\Api\CAT\Application\SearchProducts\SearchProductsQuery;
use Gaby\Apps\Api\CAT\Application\UpdateCategory\UpdateCategoryCommand;
use Gaby\Apps\Api\CAT\Application\UpdateCategory\UpdateCategoryHandler;
use Gaby\Apps\Api\CAT\Application\UpdateProduct\UpdateProductCommand;
use Gaby\Apps\Api\CAT\Application\UpdateProduct\UpdateProductHandler;
use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNameAlreadyUsedException;
use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNotFoundException;
use Gaby\Apps\Api\CAT\Domain\Exception\NegativeStockException;
use Gaby\Apps\Api\CAT\Domain\Exception\ProductNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StockAdjustment;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CatalogController
{
    use JsonResponseTrait;

    /** @var ListCatalogHandler */        private $listHandler;
    /** @var SearchProductsHandler */     private $searchHandler;
    /** @var CreateCategoryHandler */     private $createCatHandler;
    /** @var UpdateCategoryHandler */     private $updateCatHandler;
    /** @var DeactivateCategoryHandler */ private $deactivateCatHandler;
    /** @var CreateProductHandler */      private $createProdHandler;
    /** @var UpdateProductHandler */      private $updateProdHandler;
    /** @var DeactivateProductHandler */  private $deactivateProdHandler;
    /** @var AdjustStockHandler */        private $adjustStockHandler;
    /** @var GetStockHistoryHandler */    private $stockHistoryHandler;

    public function __construct(
        ListCatalogHandler $listHandler,
        SearchProductsHandler $searchHandler,
        CreateCategoryHandler $createCatHandler,
        UpdateCategoryHandler $updateCatHandler,
        DeactivateCategoryHandler $deactivateCatHandler,
        CreateProductHandler $createProdHandler,
        UpdateProductHandler $updateProdHandler,
        DeactivateProductHandler $deactivateProdHandler,
        AdjustStockHandler $adjustStockHandler,
        GetStockHistoryHandler $stockHistoryHandler
    ) {
        $this->listHandler           = $listHandler;
        $this->searchHandler         = $searchHandler;
        $this->createCatHandler      = $createCatHandler;
        $this->updateCatHandler      = $updateCatHandler;
        $this->deactivateCatHandler  = $deactivateCatHandler;
        $this->createProdHandler     = $createProdHandler;
        $this->updateProdHandler     = $updateProdHandler;
        $this->deactivateProdHandler = $deactivateProdHandler;
        $this->adjustStockHandler    = $adjustStockHandler;
        $this->stockHistoryHandler   = $stockHistoryHandler;
    }

    public function list(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        return $this->json($response, $this->listHandler->handle(new ListCatalogQuery($storeId)));
    }

    // GET /catalog/categories — lista plana de categorías
    public function listCategories(Request $request, Response $response): Response
    {
        $storeId    = (string) $request->getAttribute('storeId');
        $categories = [];
        foreach ($this->listHandler->handle(new ListCatalogQuery($storeId)) as $cat) {
            unset($cat['products']);
            $categories[] = $cat;
        }
        return $this->json($response, ['categories' => $categories]);
    }

    // GET /catalog/products — lista plana de todos los productos
    public function listProducts(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $params  = $request->getQueryParams();
        $search  = isset($params['search']) && $params['search'] !== '' ? (string) $params['search'] : null;
        $status  = isset($params['status'])  && $params['status']  !== '' ? (string) $params['status']  : null;

        $catalog  = $this->listHandler->handle(new ListCatalogQuery($storeId));
        $products = [];
        foreach ($catalog as $cat) {
            foreach ($cat['products'] as $p) {
                if ($status !== null && $p['status'] !== $status) continue;
                if ($search  !== null && stripos($p['name'], $search) === false) continue;
                $p['category_name'] = $cat['name'];
                $products[] = $p;
            }
        }
        return $this->json($response, ['products' => $products]);
    }

    public function search(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $q       = $request->getQueryParams()['q'] ?? ($request->getQueryParams()['search'] ?? null);
        return $this->json($response, $this->searchHandler->handle(new SearchProductsQuery($storeId, $q)));
    }

    public function createCategory(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $role    = (string) $request->getAttribute('role');
        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can manage categories.'], 403);
        }
        $data = (array) $request->getParsedBody();
        $name = isset($data['name']) ? trim((string)$data['name']) : '';
        if ($name === '') {
            return $this->json($response, ['error' => 'name is required.'], 422);
        }
        try {
            $cat = $this->createCatHandler->handle(new CreateCategoryCommand($storeId, $name));
        } catch (CategoryNameAlreadyUsedException $e) {
            return $this->json($response, ['error' => 'Category name already used.'], 409);
        }
        return $this->json($response, $cat->toArray(), 201);
    }

    public function updateCategory(Request $request, Response $response, array $args): Response
    {
        $storeId    = (string) $request->getAttribute('storeId');
        $role       = (string) $request->getAttribute('role');
        $categoryId = (string) ($args['id'] ?? '');
        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can manage categories.'], 403);
        }
        $data = (array) $request->getParsedBody();
        $name = isset($data['name']) ? trim((string)$data['name']) : '';
        if ($name === '') {
            return $this->json($response, ['error' => 'name is required.'], 422);
        }
        try {
            $this->updateCatHandler->handle(new UpdateCategoryCommand($storeId, $categoryId, $name));
        } catch (CategoryNotFoundException $e) {
            return $this->json($response, ['error' => 'Category not found.'], 404);
        } catch (CategoryNameAlreadyUsedException $e) {
            return $this->json($response, ['error' => 'Category name already used.'], 409);
        }
        return $this->json($response, ['id' => $categoryId, 'name' => $name]);
    }

    public function deactivateCategory(Request $request, Response $response, array $args): Response
    {
        $storeId    = (string) $request->getAttribute('storeId');
        $role       = (string) $request->getAttribute('role');
        $categoryId = (string) ($args['id'] ?? '');
        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can manage categories.'], 403);
        }
        try {
            $productCount = $this->deactivateCatHandler->handle(new DeactivateCategoryCommand($storeId, $categoryId));
        } catch (CategoryNotFoundException $e) {
            return $this->json($response, ['error' => 'Category not found.'], 404);
        }
        return $this->json($response, ['deactivated' => true, 'activeProductsMoved' => $productCount]);
    }

    public function createProduct(Request $request, Response $response): Response
    {
        $storeId    = (string) $request->getAttribute('storeId');
        $data       = (array) $request->getParsedBody();
        $name       = isset($data['name'])       ? trim((string)$data['name'])       : '';
        $categoryId = isset($data['categoryId']) ? trim((string)$data['categoryId']) : '';
        $unit       = isset($data['unit'])       ? trim((string)$data['unit'])       : '';
        $brand      = isset($data['brand'])      ? (trim((string)$data['brand']) ?: null) : null;
        $price      = isset($data['price'])      ? (int)$data['price']               : null;
        $stock      = isset($data['stock'])      ? (int)$data['stock']               : 0;

        if ($name === '' || $categoryId === '' || $unit === '') {
            return $this->json($response, ['error' => 'name, categoryId and unit are required.'], 422);
        }
        if ($price !== null && $price <= 0) {
            return $this->json($response, ['error' => 'price must be greater than zero or omitted.'], 422);
        }
        if ($stock < 0) {
            return $this->json($response, ['error' => 'stock cannot be negative.'], 422);
        }
        try {
            $product = $this->createProdHandler->handle(
                new CreateProductCommand($storeId, $categoryId, $name, $brand, $unit, $price, $stock)
            );
        } catch (CategoryNotFoundException $e) {
            return $this->json($response, ['error' => 'Category not found.'], 404);
        }
        return $this->json($response, $product->toArray(), 201);
    }

    public function updateProduct(Request $request, Response $response, array $args): Response
    {
        $storeId    = (string) $request->getAttribute('storeId');
        $productId  = (string) ($args['id'] ?? '');
        $data       = (array) $request->getParsedBody();
        $name       = isset($data['name'])       ? trim((string)$data['name'])       : '';
        $categoryId = isset($data['categoryId']) ? trim((string)$data['categoryId']) : '';
        $unit       = isset($data['unit'])       ? trim((string)$data['unit'])       : '';
        $brand      = isset($data['brand'])      ? (trim((string)$data['brand']) ?: null) : null;
        $price      = array_key_exists('price', $data) ? ($data['price'] !== null ? (int)$data['price'] : null) : null;

        if ($name === '' || $categoryId === '' || $unit === '') {
            return $this->json($response, ['error' => 'name, categoryId and unit are required.'], 422);
        }
        if ($price !== null && $price <= 0) {
            return $this->json($response, ['error' => 'price must be greater than zero or omitted.'], 422);
        }
        try {
            $this->updateProdHandler->handle(
                new UpdateProductCommand($storeId, $productId, $name, $brand, $categoryId, $unit, $price)
            );
        } catch (ProductNotFoundException $e) {
            return $this->json($response, ['error' => 'Product not found.'], 404);
        } catch (CategoryNotFoundException $e) {
            return $this->json($response, ['error' => 'Category not found.'], 404);
        }
        return $this->json($response, ['id' => $productId, 'name' => $name]);
    }

    public function deactivateProduct(Request $request, Response $response, array $args): Response
    {
        $storeId   = (string) $request->getAttribute('storeId');
        $role      = (string) $request->getAttribute('role');
        $productId = (string) ($args['id'] ?? '');
        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can deactivate products.'], 403);
        }
        try {
            $this->deactivateProdHandler->handle(new DeactivateProductCommand($storeId, $productId));
        } catch (ProductNotFoundException $e) {
            return $this->json($response, ['error' => 'Product not found.'], 404);
        }
        return $response->withStatus(204);
    }

    public function adjustStock(Request $request, Response $response, array $args): Response
    {
        $storeId   = (string) $request->getAttribute('storeId');
        $adminName = (string) $request->getAttribute('adminId');
        $productId = (string) ($args['id'] ?? '');
        $data      = (array) $request->getParsedBody();
        $newStock  = isset($data['newStock']) ? (int)$data['newStock'] : -1;
        $reason    = isset($data['reason'])   ? trim((string)$data['reason'])  : '';
        $note      = isset($data['note'])     ? (trim((string)$data['note']) ?: null) : null;

        if (!in_array($reason, StockAdjustment::$validReasons, true)) {
            return $this->json($response, ['error' => 'reason must be one of: ' . implode(', ', StockAdjustment::$validReasons)], 422);
        }
        if ($newStock < 0) {
            return $this->json($response, ['error' => 'newStock must be zero or positive.'], 422);
        }
        try {
            $adj = $this->adjustStockHandler->handle(
                new AdjustStockCommand($storeId, $productId, $newStock, $reason, $note, $adminName)
            );
        } catch (ProductNotFoundException $e) {
            return $this->json($response, ['error' => 'Product not found.'], 404);
        } catch (NegativeStockException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 422);
        }
        return $this->json($response, $adj->toArray());
    }

    public function stockHistory(Request $request, Response $response, array $args): Response
    {
        $storeId   = (string) $request->getAttribute('storeId');
        $productId = (string) ($args['id'] ?? '');
        try {
            $history = $this->stockHistoryHandler->handle(new GetStockHistoryQuery($storeId, $productId));
        } catch (ProductNotFoundException $e) {
            return $this->json($response, ['error' => 'Product not found.'], 404);
        }
        return $this->json($response, $history);
    }
}
