<?php

namespace EDB\AdminBundle\ListBuilder;

use EDB\AdminBundle\Collection\AbstractCollectionElement;

class ActionGroup extends AbstractCollectionElement
{
    public const OPTION_TEMPLATE = 'template';

    protected array $actions;

    public function __construct(array $actions, array $options = [], string $name = 'Actions')
    {
        parent::__construct($name, $options);

        $this->actions = $actions;
    }

    public function getActions(): array
    {
        return $this->actions;
    }
}
