<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use Exception;

class Pool
{
    /**
     * @var AdminInterface
     */
    private array $admins = [];

    public function __construct($admins)
    {
        foreach ($admins as $admin) {
            $this->admins[$admin->getEntityClass()] = $admin;
        }
    }

    public function getAdminForClass(string $class): AdminInterface
    {
        if (!isset($this->admins[$class])) {
            throw new Exception(sprintf('No Admin found for class "%s".', $class));
        }

        return $this->admins[$class];
    }

    public function getAdmins(): array
    {
        return $this->admins;
    }
}
