<?php


namespace Djvue\WpAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('wp_admin');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('host')->end()
                ->scalarNode('table_prefix')->end()
                ->arrayNode('database')
                    ->children()
                    ->scalarNode('host')->end()
                    ->scalarNode('port')->end()
                    ->scalarNode('name')->end()
                    ->scalarNode('user')->end()
                    ->scalarNode('password')->end()
                    ->end()
                ->end()
                ->arrayNode('namespaces')
                    ->children()
                    ->scalarNode('configurator')->end()
                    ->scalarNode('field_group')->end()
                    ->end()
                ->end()
                ->scalarNode('page_templates_path')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
