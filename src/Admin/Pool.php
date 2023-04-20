<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use Exception;

class Pool
{
    private array $admins = [];

    public function __construct(iterable $admins)
    {
        foreach ($admins as $admin) {
            $this->admins[$admin->getEntityClass()] = $admin;
        }
    }

    public function getAdminForClass(string $class): AdminInterface
    {
        if (!isset($this->admins[$class])) {
            throw new Exception(sprintf('No admin found for class "%s".', $class));
        }

        return $this->admins[$class];
    }

    public function getAdmins(): array
    {
        return $this->admins;
    }
}
