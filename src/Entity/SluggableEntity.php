<?php

namespace EDB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EDB\AdminBundle\Util\StringUtils;

/**
 * IMPORTANT: Make sure that the entity who is using this trait has
 * ORM\HasLifecycleCallbacks enabled!
 */
trait SluggableEntity
{
    /**
     * @ORM\Column
     */
    protected $slug;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateSlug(): void
    {
        if (empty($this->getSlug())) {
            $this->setSlug(StringUtils::createSlug((string)$this));
        }
    }
}
