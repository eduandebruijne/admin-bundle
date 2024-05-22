<?php

namespace EDB\AdminBundle\Entity;

use Doctrine\ORM\Mapping\Column;

trait SortableEntity
{
    #[Column(type: 'integer')]
    protected ?int $position = 0;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
