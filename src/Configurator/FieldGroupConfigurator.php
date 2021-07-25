<?php

namespace Djvue\WpAdminBundle\Configurator;

use Djvue\WpAdminBundle\Helper\DirectoryClassContainer;
use Djvue\WpAdminBundle\Interfaces\Registrable;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\CacheInterface;

class FieldGroupConfigurator implements ConfiguratorInterface
{
    public const CACHE_PREFIX = 'wp-admin-bundle_';
    protected bool $enableCache;
    protected string $baseNamespace;

    public function __construct(
        private CacheInterface $cache,
        private DirectoryClassContainer $directoryClassContainer,
        ParameterBagInterface $parameterBag,
        KernelInterface $kernel,
    )
    {
        $this->enableCache = !$kernel->isDebug() && $parameterBag->get('wp_admin.enable_cache');
        $this->baseNamespace = $parameterBag->get('wp_admin.namespaces.field_group');
    }

    public function run(): void
    {
        $fieldGroups = $this->directoryClassContainer->getClasses($this->baseNamespace);
        foreach ($fieldGroups as $group) {
            if ($group instanceof Registrable) {
                $group->setMaybeCacheFn(function (string $class, callable $fn) {
                    if ($this->enableCache) {
                        $key = self::CACHE_PREFIX.str_replace('\\', '_', $class);
                        return $this->cache->get($key, $fn);
                    }
                    return $fn();
                });
                add_action('plugins_loaded', fn() => $group->register());
            }
        }
    }
}
