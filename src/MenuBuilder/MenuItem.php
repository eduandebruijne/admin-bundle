<?php

declare(strict_types=1);

namespace EDB\AdminBundle\MenuBuilder;

use EDB\AdminBundle\Collection\AbstractCollectionElement;

class MenuItem extends AbstractCollectionElement
{
    public const OPTION_GROUP = 'group';
    public const OPTION_ORDER_NAME = 'order-name';
    public const OPTION_PATH = 'path';
    public const OPTION_ICON = 'icon';
    public const OPTION_SHOW_IN_DASHBOARD = 'show-in-dashboard';

    public function __construct(
        protected string $name,
        protected array $options = [],
    ) {
        parent::__construct(
            $name,
            array_merge(
                [self::OPTION_SHOW_IN_DASHBOARD => true],
                $this->options
            )
        );
    }
}
