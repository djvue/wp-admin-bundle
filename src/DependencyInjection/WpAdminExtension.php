<?php


namespace Djvue\WpAdminBundle\DependencyInjection;

use Djvue\WpAdminBundle\Configurator\AbstractConfigurator;
use Djvue\WpAdminBundle\Configurator\ConfiguratorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WpAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(ConfiguratorInterface::class)
            ->addTag('wp_admin.configurator');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('wp_admin.enable_cache', $config['enable_cache']);
        $container->setParameter('wp_admin.host', $config['host']);
        $container->setParameter('wp_admin.table_prefix', $config['table_prefix']);
        $container->setParameter('wp_admin.database', $config['database']);
        $container->setParameter('wp_admin.namespaces.configurator', $config['namespaces']['configurator']);
        $container->setParameter('wp_admin.namespaces.field_group', $config['namespaces']['field_group']);
        $container->setParameter('wp_admin.page_templates', $config['page_templates']);
        $container->setParameter('wp_admin.page_templates_path', $config['page_templates_path']);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yaml');
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}
