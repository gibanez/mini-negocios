<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\CancelPayment;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\Exception\MovementAlreadyCancelledException;
use Gaby\Apps\Api\FIA\Domain\Exception\MovementNotFoundException;
final class CancelPaymentHandler
{
    /** @var CreditAccountRepositoryInterface */ private $repo;
    public function __construct(CreditAccountRepositoryInterface $repo) { $this->repo = $repo; }
    public function handle(CancelPaymentCommand $cmd): void
    {
        $movement = $this->repo->findMovementById($cmd->movementId);
        if ($movement === null || $movement->storeId() !== $cmd->storeId) {
            throw MovementNotFoundException::withId($cmd->movementId);
        }
        if (!$movement->isActive()) {
            throw MovementAlreadyCancelledException::create();
        }
        $movement->cancel($cmd->reason, $cmd->cancelledBy);
        $this->repo->saveMovement($movement);
    }
}
