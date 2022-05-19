<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Entity;

interface SortableEntityInterface
{
    public function getPosition(): ?int;

    public function setPosition(int $position): void;
}
