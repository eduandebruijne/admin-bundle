<?php

namespace EDB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SortableEntity
{
    /**
     * @ORM\Column(type="integer")
     * @var ?int
     */
    private $position = 0;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
