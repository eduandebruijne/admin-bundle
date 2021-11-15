<?php

namespace EDB\AdminBundle\EntityEvent;

abstract class AbstractEntityEventHandler implements EntityEventHandlerInterface
{
    public function supports(): array
    {
        return ["*"];
    }

    public function getPriority(): int
    {
        return 100;
    }
}
