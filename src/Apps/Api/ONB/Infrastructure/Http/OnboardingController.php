<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Infrastructure\Http;

use Gaby\Apps\Api\ONB\Application\GetOnboardingStatus\GetOnboardingStatusQuery;
use Gaby\Apps\Api\ONB\Application\GetOnboardingStatus\GetOnboardingStatusHandler;
use Gaby\Apps\Api\ONB\Application\SetStoreName\SetStoreNameCommand;
use Gaby\Apps\Api\ONB\Application\SetStoreName\SetStoreNameHandler;
use Gaby\Apps\Api\ONB\Application\SetStoreTheme\SetStoreThemeCommand;
use Gaby\Apps\Api\ONB\Application\SetStoreTheme\SetStoreThemeHandler;
use Gaby\Apps\Api\ONB\Application\ChooseIndustry\ChooseIndustryCommand;
use Gaby\Apps\Api\ONB\Application\ChooseIndustry\ChooseIndustryHandler;
use Gaby\Apps\Api\ONB\Application\CompleteOnboarding\CompleteOnboardingCommand;
use Gaby\Apps\Api\ONB\Application\CompleteOnboarding\CompleteOnboardingHandler;
use Gaby\Apps\Api\ONB\Domain\Exception\OnboardingAlreadyCompletedException;
use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class OnboardingController
{
    use JsonResponseTrait;

    /** @var GetOnboardingStatusHandler */
    private $statusHandler;
    /** @var SetStoreNameHandler */
    private $setNameHandler;
    /** @var SetStoreThemeHandler */
    private $setThemeHandler;
    /** @var ChooseIndustryHandler */
    private $chooseIndustryHandler;
    /** @var CompleteOnboardingHandler */
    private $completeHandler;

    public function __construct(
        GetOnboardingStatusHandler $statusHandler,
        SetStoreNameHandler $setNameHandler,
        SetStoreThemeHandler $setThemeHandler,
        ChooseIndustryHandler $chooseIndustryHandler,
        CompleteOnboardingHandler $completeHandler
    ) {
        $this->statusHandler         = $statusHandler;
        $this->setNameHandler        = $setNameHandler;
        $this->setThemeHandler       = $setThemeHandler;
        $this->chooseIndustryHandler = $chooseIndustryHandler;
        $this->completeHandler       = $completeHandler;
    }

    public function status(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        try {
            $data = $this->statusHandler->handle(new GetOnboardingStatusQuery($storeId));
            return $this->json($response, $data);
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 404);
        }
    }

    public function setName(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $body    = (array) $request->getParsedBody();
        $name    = isset($body['name']) && $body['name'] !== '' ? (string) $body['name'] : null;

        if ($name === null) {
            return $this->json($response, ['error' => 'name is required.'], 422);
        }

        try {
            $this->setNameHandler->handle(new SetStoreNameCommand($storeId, $name));
            return $this->json($response, ['name' => $name]);
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 404);
        }
    }

    public function setTheme(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $body    = (array) $request->getParsedBody();
        $theme   = isset($body['theme']) && $body['theme'] !== '' ? (string) $body['theme'] : null;

        if ($theme === null) {
            return $this->json($response, ['error' => 'theme is required.'], 422);
        }

        try {
            $this->setThemeHandler->handle(new SetStoreThemeCommand($storeId, $theme));
            return $this->json($response, ['theme' => $theme]);
        } catch (\InvalidArgumentException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 422);
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 404);
        }
    }

    public function chooseIndustry(Request $request, Response $response): Response
    {
        $storeId    = (string) $request->getAttribute('storeId');
        $body       = (array) $request->getParsedBody();
        $industryId = isset($body['industryId']) && $body['industryId'] !== ''
            ? (string) $body['industryId']
            : (isset($body['industry_id']) && $body['industry_id'] !== '' ? (string) $body['industry_id'] : null);

        if ($industryId === null) {
            return $this->json($response, ['error' => 'industryId is required.'], 422);
        }

        try {
            $result = $this->chooseIndustryHandler->handle(new ChooseIndustryCommand($storeId, $industryId));
            return $this->json($response, $result);
        } catch (\InvalidArgumentException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 422);
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 404);
        }
    }

    public function complete(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $body    = (array) $request->getParsedBody();

        // Aplicar nombre y tema si vienen en el body (el frontend los manda juntos)
        if (!empty($body['store_name'])) {
            try {
                $this->setNameHandler->handle(new SetStoreNameCommand($storeId, (string) $body['store_name']));
            } catch (\Throwable $e) { /* no fatal */ }
        }
        if (!empty($body['theme'])) {
            try {
                $this->setThemeHandler->handle(new SetStoreThemeCommand($storeId, (string) $body['theme']));
            } catch (\Throwable $e) { /* no fatal */ }
        }

        try {
            $this->completeHandler->handle(new CompleteOnboardingCommand($storeId));
        } catch (OnboardingAlreadyCompletedException $e) {
            // idempotente: ya estaba completado, no es un error
        } catch (StoreNotFoundException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 404);
        }
        return $this->json($response, ['completed' => true]);
    }
}
