<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Application\DeactivateVendor;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;
use Gaby\Apps\Api\Personal\Domain\Exception\LastOwnerException;
use Gaby\Apps\Api\Personal\Domain\Exception\VendorNotFoundException;

final class DeactivateVendorHandler
{
    /** @var AdminRepositoryInterface */
    private $admins;

    public function __construct(AdminRepositoryInterface $admins)
    {
        $this->admins = $admins;
    }

    public function handle(DeactivateVendorCommand $command): void
    {
        $member = $this->admins->findById($command->vendorId);

        if ($member === null || $member->storeId() !== $command->storeId) {
            throw VendorNotFoundException::withId($command->vendorId);
        }

        if ($member->isOwner()) {
            $activeOwners = $this->admins->countActiveOwnersByStoreId($command->storeId);
            if ($activeOwners <= 1) {
                throw LastOwnerException::create();
            }
        }

        $member->deactivate();
        $this->admins->save($member);
    }
}
