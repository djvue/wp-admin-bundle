<?php


namespace Djvue\WpAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WpAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('wp.host', $config['host']);
        $container->setParameter('wp.table_prefix', $config['table_prefix']);
        $container->setParameter('wp.database', $config['database']);
        $container->setParameter('wp.namespaces.configurator', $config['namespaces']['configurator']);
        $container->setParameter('wp.namespaces.field_group', $config['namespaces']['field_group']);
        $container->setParameter('wp.page_templates_path', $config['page_templates_path']);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yaml');
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}
