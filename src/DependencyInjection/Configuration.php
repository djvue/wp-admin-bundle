<?php

declare(strict_types=1);

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
            ->booleanNode('enable_options_cache')->defaultValue('true')->end()
            ->booleanNode('enable_post_fields_cache')->defaultValue('true')->end()
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
            ->arrayNode('page_templates')
            ->defaultValue([])
            ->arrayPrototype()
            ->children()
            ->scalarNode('key')->end()
            ->scalarNode('name')->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('routing')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('post_prefix')->defaultValue('/blog/')->end()
            ->end()
            ->end()
            ->scalarNode('autoload')->defaultValue(true)->end()
            ->scalarNode('default_timezone')->defaultValue('Europe/Moscow')->end()
            ->scalarNode('content_directory')->defaultValue('public/content')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
