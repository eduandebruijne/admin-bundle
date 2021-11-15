<?php

namespace EDB\AdminBundle\EntityEvent;

use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\Entity\SluggableEntity;
use EDB\AdminBundle\Util\StringUtils;

class UpdateSlug extends AbstractEntityEventHandler
{
    public function supports(): array
    {
        return [SluggableEntity::class];
    }

    public function handle(BaseEntity $entity, string $context)
    {
        /** @var SluggableEntity $entity */
        if (empty($entity->getSlug())) {
            $entity->setSlug(StringUtils::createSlug((string)$entity));
        }
    }
}
