<?php

namespace EDB\AdminBundle\DependencyInjection;

use EDB\AdminBundle\Admin\AdminInterface;
use EDB\AdminBundle\MenuBuilder\MenuItemInterface;
use EDB\AdminBundle\Route\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class EDBAdminExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $twig = $container->getExtensionConfig('twig')[0];
        $twig['globals']['media_path'] = $container->getParameter('media_path');
        $twig['form_themes'] = [
            'bootstrap_5_layout.html.twig',
            '@EDBAdmin/form/theme.html.twig'
        ];
        $container->prependExtensionConfig('twig', $twig);

        $loaderDefinition = new Definition(Loader::class);
        $loaderDefinition->setTags(['routing.loader']);
        $container->setDefinition(Loader::class, $loaderDefinition);

        $servicesByInstanceOf = $container->getAutoconfiguredInstanceof();

        if (key_exists(AdminInterface::class, $servicesByInstanceOf)) {
            # TODO: Check if this works, if it does, tag these services
            $h=1;
        }

        if (key_exists(MenuItemInterface::class, $servicesByInstanceOf)) {
            # TODO: Check if this works, if it does, tag these services
            $h=1;
        }

        $adminServices = $container->findTaggedServiceIds('edb.admin');
        $adminMenuItemServices = $container->findTaggedServiceIds('edb.menu_item');
    }
}
