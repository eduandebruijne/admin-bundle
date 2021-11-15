<?php

declare(strict_types=1);

namespace EDB\AdminBundle\FormBuilder;

use EDB\AdminBundle\Collection\AbstractCollection;

class FormCollection extends AbstractCollection
{
    public function add($name, $type, $options = []): FormCollection
    {
        $this->elements[] = new FormItem($name, $type, $options);
        return $this;
    }
}
