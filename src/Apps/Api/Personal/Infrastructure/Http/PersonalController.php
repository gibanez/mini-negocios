<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Infrastructure\Http;

use Gaby\Apps\Api\ACC\Domain\Exception\EmailAlreadyInUseException;
use Gaby\Apps\Api\Personal\Application\CreateVendor\CreateVendorCommand;
use Gaby\Apps\Api\Personal\Application\CreateVendor\CreateVendorHandler;
use Gaby\Apps\Api\Personal\Application\DeactivateVendor\DeactivateVendorCommand;
use Gaby\Apps\Api\Personal\Application\DeactivateVendor\DeactivateVendorHandler;
use Gaby\Apps\Api\Personal\Application\ListPersonal\ListPersonalHandler;
use Gaby\Apps\Api\Personal\Application\ListPersonal\ListPersonalQuery;
use Gaby\Apps\Api\Personal\Domain\Exception\LastOwnerException;
use Gaby\Apps\Api\Personal\Domain\Exception\VendorNotFoundException;
use Gaby\Shared\Http\JsonResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PersonalController
{
    use JsonResponseTrait;

    /** @var ListPersonalHandler */
    private $listHandler;

    /** @var CreateVendorHandler */
    private $createHandler;

    /** @var DeactivateVendorHandler */
    private $deactivateHandler;

    public function __construct(
        ListPersonalHandler $listHandler,
        CreateVendorHandler $createHandler,
        DeactivateVendorHandler $deactivateHandler
    ) {
        $this->listHandler       = $listHandler;
        $this->createHandler     = $createHandler;
        $this->deactivateHandler = $deactivateHandler;
    }

    public function list(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $members = $this->listHandler->handle(new ListPersonalQuery($storeId));
        return $this->json($response, $members);
    }

    public function create(Request $request, Response $response): Response
    {
        $storeId = (string) $request->getAttribute('storeId');
        $role    = (string) $request->getAttribute('role');

        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can manage personal.'], 403);
        }

        $data     = (array) $request->getParsedBody();
        $name     = isset($data['name'])     ? trim((string) $data['name'])  : '';
        $email    = isset($data['email'])    ? trim((string) $data['email']) : '';
        $password = isset($data['password']) ? (string) $data['password']    : '';

        if ($name === '' || $email === '' || $password === '') {
            return $this->json($response, ['error' => 'name, email and password are required.'], 422);
        }

        if (strlen($password) < 8) {
            return $this->json($response, ['error' => 'Password must be at least 8 characters.'], 422);
        }

        try {
            $vendor = $this->createHandler->handle(new CreateVendorCommand($storeId, $name, $email, $password));
        } catch (EmailAlreadyInUseException $e) {
            return $this->json($response, ['error' => 'Email already in use.'], 409);
        }

        return $this->json($response, [
            'id'     => $vendor->id(),
            'name'   => $vendor->name(),
            'email'  => $vendor->email(),
            'role'   => $vendor->role(),
            'status' => $vendor->status(),
        ], 201);
    }

    public function deactivate(Request $request, Response $response, array $args): Response
    {
        $storeId  = (string) $request->getAttribute('storeId');
        $role     = (string) $request->getAttribute('role');
        $vendorId = (string) ($args['id'] ?? '');

        if ($role !== 'owner') {
            return $this->json($response, ['error' => 'Only owners can manage personal.'], 403);
        }

        try {
            $this->deactivateHandler->handle(new DeactivateVendorCommand($storeId, $vendorId));
        } catch (VendorNotFoundException $e) {
            return $this->json($response, ['error' => 'Member not found.'], 404);
        } catch (LastOwnerException $e) {
            return $this->json($response, ['error' => $e->getMessage()], 409);
        }

        return $response->withStatus(204);
    }
}
