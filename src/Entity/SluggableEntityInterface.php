<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Entity;

interface SluggableEntityInterface
{
    public function getSlug(): ?string;

    public function setSlug(?string $slug): void;

    public function updateSlug(): void;
}
