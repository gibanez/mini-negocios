<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Application\CreateVendor;

use Gaby\Apps\Api\ACC\Domain\Exception\EmailAlreadyInUseException;
use Gaby\Apps\Api\Admin\Domain\Admin;
use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class CreateVendorHandler
{
    /** @var AdminRepositoryInterface */
    private $admins;

    public function __construct(AdminRepositoryInterface $admins)
    {
        $this->admins = $admins;
    }

    public function handle(CreateVendorCommand $command): Admin
    {
        $existing = $this->admins->findByEmail($command->email);

        if ($existing !== null) {
            throw EmailAlreadyInUseException::withEmail($command->email);
        }

        $vendor = Admin::createVendor($command->name, $command->email, $command->password, $command->storeId);
        $this->admins->save($vendor);

        return $vendor;
    }
}
