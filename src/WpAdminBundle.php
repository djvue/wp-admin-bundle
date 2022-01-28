<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle;

use Djvue\WpAdminBundle\DependencyInjection\WpAdminBundlePass;
use Djvue\WpAdminBundle\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\AutowiringFailedException;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class WpAdminBundle extends Bundle
{
    private static LoaderInterface $loader;

    public function boot(): void
    {

        /**
         * @var LoaderInterface $loader
         */
        $loader = $this->container->get(LoaderInterface::class);
        if ($loader === null) {
            throw new AutowiringFailedException('Dependency implements LoaderInterface not found');
        }
        self::$loader = $loader;

        if ($this->needAutoload()) {
            $loader->loadCore();
        }
    }

    private function needAutoload(): bool
    {
        $env = $this->container->getParameter('kernel.environment');

        if (defined('WP_ADMIN')) {
            return true;
        }
        if (!$this->container->getParameter('wp_admin.autoload')) {
            return false;
        }
        if ($env === 'test') {
            return false;
        }

        return true;
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
