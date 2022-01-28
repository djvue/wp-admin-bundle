<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\DependencyInjection;

use Djvue\WpAdminBundle\Configurator\AbstractConfigurator;
use Djvue\WpAdminBundle\Configurator\ConfiguratorInterface;
use Djvue\WpAdminBundle\FieldGroup\FieldGroupInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WpAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(ConfiguratorInterface::class)
            ->addTag('wp_admin.configurator')
        ;
        $container->registerForAutoconfiguration(FieldGroupInterface::class)
            ->addTag('wp_admin.field_group')
        ;

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('wp_admin.enable_cache', $config['enable_cache']);
        $container->setParameter('wp_admin.enable_options_cache', $config['enable_options_cache']);
        $container->setParameter('wp_admin.enable_post_fields_cache', $config['enable_post_fields_cache']);
        $container->setParameter('wp_admin.host', $config['host']);
        $container->setParameter('wp_admin.table_prefix', $config['table_prefix']);
        $container->setParameter('wp_admin.database', $config['database']);
        $container->setParameter('wp_admin.page_templates', $config['page_templates']);
        $container->setParameter('wp_admin.default_timezone', $config['default_timezone']);
        $container->setParameter('wp_admin.autoload', $config['autoload']);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yaml');
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}
