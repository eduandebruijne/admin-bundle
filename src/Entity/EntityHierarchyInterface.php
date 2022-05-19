<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface EntityHierarchyInterface
{
    public function getParent(): ?BaseEntity;

    public function getChildren(): array;
}
