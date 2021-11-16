<?php

namespace EDB\AdminBundle\Entity;

use EDB\AdminBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class SortableEntity extends BaseEntity
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
