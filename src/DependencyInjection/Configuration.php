<?php

declare(strict_types=1);

namespace EDB\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('edb-admin');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('admin_icon')->end()
            ->scalarNode('admin_path')->end()
            ->scalarNode('admin_title')->end()
            ->scalarNode('cache_prefix')->end()
            ->scalarNode('media_class')->end()
            ->scalarNode('media_path')->end()
            ->scalarNode('source_prefix')->end()
            ->scalarNode('user_class')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
