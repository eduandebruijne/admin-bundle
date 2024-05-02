<?php

namespace EDB\AdminBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use EDB\AdminBundle\Util\StringUtils;

/**
 * IMPORTANT: Make sure to add #[MappedSuperclass] when extending this class
 */
trait SluggableEntity
{
    #[Column(type: 'string')]
    protected $slug;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    #[PrePersist]
    #[PreUpdate]
    public function updateSlug(): void
    {
        if (empty($this->getSlug())) {
            $this->setSlug(StringUtils::createSlug((string)$this));
        }
    }
}
