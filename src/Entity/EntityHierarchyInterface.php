<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Entity;

interface EntityHierarchyInterface
{
    public function getParent(): ?BaseEntity;

    public function setParent(BaseEntity $parent): void;

    public function getChildren(): array;

    public function addChild(BaseEntity $child): void;

    public function removeChild(BaseEntity $child): void;
}
