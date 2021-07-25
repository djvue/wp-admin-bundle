<?php
namespace Djvue\WpAdminBundle;

use Djvue\WpAdminBundle\DependencyInjection\WpAdminBundlePass;
use Djvue\WpAdminBundle\Loader\LoaderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\AutowiringFailedException;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class WpAdminBundle extends Bundle
{
    private static LoaderInterface $loader;

    public function boot(): void
    {
        $env = $this->container->getParameter('kernel.environment');

        if ($env !== 'test') {
            $loader = $this->container->get(LoaderInterface::class);
            if ($loader === null) {
                throw new AutowiringFailedException('Dependency implements LoaderInterface not found');
            }
            self::$loader = $loader;

            $loader->loadCore();
        }
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WpAdminBundlePass());
    }

    public static function getLoader(): LoaderInterface
    {
        if (!isset(self::$loader)) {
            throw new \RuntimeException('WpBundle should be initialized before loading Wordpress');
        }
        return self::$loader;
    }
}
