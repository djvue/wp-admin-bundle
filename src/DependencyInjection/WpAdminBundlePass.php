<?php


namespace Djvue\WpAdminBundle\DependencyInjection;


use Djvue\WpAdminBundle\Loader\ConfigurationLoader;
use Djvue\WpAdminBundle\Loader\ConfigurationLoaderInterface;
use Djvue\WpAdminBundle\Loader\Loader;
use Djvue\WpAdminBundle\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WpAdminBundlePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // $container->setAlias(LoaderInterface::class, Loader::class)->setPublic(true);
        // $container->autowire(Loader::class, Loader::class);
        // $container->setAlias(ConfigurationLoaderInterface::class, ConfigurationLoader::class)->setPublic(true);
        // $container->autowire(ConfigurationLoader::class);
    }
}
