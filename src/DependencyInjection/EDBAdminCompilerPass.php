<?php

declare(strict_types=1);

namespace EDB\AdminBundle\DependencyInjection;

use EDB\AdminBundle\Admin\Pool;
use EDB\AdminBundle\MenuBuilder\MenuBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SecurityBundle\Security;

class EDBAdminCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $poolDefinition = new Definition(Pool::class);
        $poolDefinition->addArgument($this->getServicesByTag($container, 'edb.admin'));

        $container->setDefinition(Pool::class, $poolDefinition);

        $menuBuilderDefinition = new Definition(MenuBuilder::class, [
            new Reference(Pool::class),
            new Reference(RouterInterface::class),
            new Reference(Security::class),
            $this->getServicesByTag($container, 'edb.menu_item')
        ]);

        $container->setDefinition(MenuBuilder::class, $menuBuilderDefinition);
    }

    protected function getServicesByTag(ContainerInterface $container, string $tag): array
    {
        $services = [];
        foreach (array_keys($container->findTaggedServiceIds($tag)) as $service) {
            $services[] = $container->getDefinition($service);
        }

        return $services;
    }
}
