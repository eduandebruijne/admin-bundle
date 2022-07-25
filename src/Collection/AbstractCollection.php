<?php

namespace EDB\AdminBundle\Collection;

abstract class AbstractCollection
{
    protected array $elements = [];

    public function getElements(): array
    {
        return $this->elements;
    }
}
