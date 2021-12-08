<?php

namespace EDB\AdminBundle\Entity;

use EDB\AdminBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use EDB\AdminBundle\Util\StringUtils;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
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

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateSlug()
    {
        if (empty($this->getSlug())) {
            $this->setSlug(StringUtils::createSlug((string)$this));
        }
    }
}
