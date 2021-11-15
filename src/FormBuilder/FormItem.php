<?php

namespace EDB\AdminBundle\FormBuilder;

use EDB\AdminBundle\Collection\AbstractCollectionElement;

class FormItem extends AbstractCollectionElement
{
    private string $type;

    public function __construct(string $name, string $type, array $options)
    {
        parent::__construct($name, $options);
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
