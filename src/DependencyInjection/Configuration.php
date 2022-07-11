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

//        $treeBuilder->getRootNode()
//            ->children()
//            ->arrayNode('twitter')
//            ->children()
//            ->integerNode('client_id')->end()
//            ->scalarNode('client_secret')->end()
//            ->end()
//            ->end() // twitter
//            ->end()
//        ;

        return $treeBuilder;
    }
}
