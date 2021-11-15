<?php

namespace EDB\AdminBundle\Entity;

use EDB\AdminBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class SluggableEntity extends BaseEntity
{
    /**
     * @ORM\Column
     */
    private $slug;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug)
    {
        $this->slug = $slug;
    }
}
