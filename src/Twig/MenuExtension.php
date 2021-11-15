<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Twig;

use EDB\AdminBundle\MenuBuilder\MenuBuilder;
use ReflectionException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    private MenuBuilder $menuBuilder;

    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_menu', [$this, 'getMenu'])
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function getMenu(): array
    {
        return $this->menuBuilder->getElements();
    }
}
