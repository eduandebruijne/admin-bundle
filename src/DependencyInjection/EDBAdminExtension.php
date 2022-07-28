<?php

namespace EDB\AdminBundle\DependencyInjection;

use EDB\AdminBundle\Admin\AdminInterface;
use EDB\AdminBundle\MenuBuilder\MenuItemInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EDBAdminExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(AdminInterface::class)->addTag('edb.admin');
        $container->registerForAutoconfiguration(MenuItemInterface::class)->addTag('edb.menu_item');

        $container->setParameter('edb_admin_icon', $configs[0]['admin_icon']);
        $container->setParameter('edb_admin_title', $configs[0]['admin_title']);
        $container->setParameter('edb_cache_prefix', $configs[0]['cache_prefix']);
        $container->setParameter('edb_media_class', $configs[0]['media_class']);
        $container->setParameter('edb_source_prefix', $configs[0]['source_prefix']);
        $container->setParameter('edb_user_class', $configs[0]['user_class']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $authClients = [
            'clients' => [
                'google' => [
                    'type' => 'google',
                    'client_id' => '%env(GOOGLE_CLIENT_ID)%',
                    'client_secret' => '%env(GOOGLE_CLIENT_SECRET)%',
                    'redirect_route' => 'connect_google_check',
                    'redirect_params' => [],
                ]
            ]
        ];

        $container->prependExtensionConfig('knpu_oauth2_client', $authClients);

        $twig = $container->getExtensionConfig('twig')[0];
        $twig['globals']['media_path'] = '%env(MEDIA_PATH)%';

        $existingThemes = isset($twig['form_themes']) ? $twig['form_themes'] : [];
        $twig['form_themes'] = array_merge($existingThemes, [
            'bootstrap_5_layout.html.twig',
            '@EDBAdmin/form/theme.html.twig'
        ]);

        $container->prependExtensionConfig('twig', $twig);
    }
}
