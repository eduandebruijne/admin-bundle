<?php

namespace EDB\AdminBundle\EntityEvent;

use EDB\AdminBundle\Entity\BaseEntity;

interface EntityEventHandlerInterface
{
    public function getPriority(): int;
    public function handle(BaseEntity $entity, string $context);
    public function supports(): array;
}
