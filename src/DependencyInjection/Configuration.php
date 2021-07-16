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
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enable_cache')->defaultValue('true')->end()
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
            ->arrayNode('page_templates')
            ->defaultValue([])
            ->arrayPrototype()
            ->children()
            ->scalarNode('key')->end()
            ->scalarNode('name')->end()
            ->end()
            ->end()
            ->end()
            ->scalarNode('page_templates_path')->defaultValue(null)->end()
            // TODO: add configurator to customize route prefix
            ->arrayNode('routing')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('post_prefix')->defaultValue('/blog/')->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
