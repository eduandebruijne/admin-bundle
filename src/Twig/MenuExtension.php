<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Twig;

use EDB\AdminBundle\MenuBuilder\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    public function __construct(
        protected MenuBuilder $menuBuilder,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_menu', [$this, 'getMenu'])
        ];
    }

    public function getMenu(): array
    {
        return $this->menuBuilder->getElements();
    }
}
