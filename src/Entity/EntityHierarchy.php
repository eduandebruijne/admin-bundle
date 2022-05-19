<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Entity;

trait EntityHierarchy
{
    public function getParent(): ?BaseEntity
    {
        return $this->parent;
    }

    public function getChildren(): array
    {
        /**
         * Assuming that 'children' will be a Doctrine Collection
         */
        return $this->children->toArray();
    }

    public function setParent(?BaseEntity $parent)
    {
        $this->parent = $parent;
    }

    public function addChild(BaseEntity $child): void
    {
        if (false === $this->children->contains($child)) {
            $child->setParent($this);
        }
    }

    public function removeChild(BaseEntity $child): void
    {
        $child->setParent(null);
    }

    public function getParentCount(): int
    {
        $seen = [];
        $count = 0;
        $currentParent = $this;

        if ($currentParent === $currentParent->getParent()) {
            // Selected itself, ignore
            return 0;
        }

        while (null !== $currentParent->getParent()) {

            if (true === in_array($currentParent->getId(), $seen)) {
                break;
            }

            $count++;
            $seen[] = $currentParent->getId();
            $currentParent = $currentParent->getParent();
        }

        return $count;
    }
}
