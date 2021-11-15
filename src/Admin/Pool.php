<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use Exception;

class Pool
{
    /** @var AdminInterface[] */
    private array $admins = [];

    /**
     * @param AdminInterface[]
     */
    public function __construct($admins)
    {
        foreach ($admins as $admin) {
            $this->admins[$admin::getEntityClass()] = $admin;
        }
    }

    /**
     * @throws Exception
     */
    public function getAdminForClass(string $class): AdminInterface
    {
        if (!isset($this->admins[$class])) {
            throw new Exception(sprintf('Admin for "%s" not found.', $class));
        }

        return $this->admins[$class];
    }

    public function getAdmins(): array
    {
        return $this->admins;
    }
}
