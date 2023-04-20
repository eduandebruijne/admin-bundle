<?php

namespace EDB\AdminBundle\FormBuilder;

use EDB\AdminBundle\Collection\AbstractCollectionElement;

class FormItem extends AbstractCollectionElement
{
    public function __construct(
        string $name,
        private string $type,
        array $options
    ) {
        parent::__construct($name, $options);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
