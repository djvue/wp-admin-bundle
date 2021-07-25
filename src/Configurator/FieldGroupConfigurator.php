<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

use Djvue\WpAdminBundle\FieldGroup\FieldGroupInterface;
use Djvue\WpAdminBundle\Service\WpFacade;
use Symfony\Contracts\Cache\CacheInterface;

class FieldGroupConfigurator implements ConfiguratorInterface
{
    public const CACHE_PREFIX = 'wp-admin-bundle_';
    protected bool $enableCache;

    public function __construct(
        /**
         * @var list<FieldGroupInterface>
         */
        private iterable $fieldGroups,
        string $enableCache,
        bool $kernelDebug,
        private CacheInterface $cache,
        private WpFacade $wp,
    ) {
        $this->enableCache = !$kernelDebug && $enableCache;
    }

    public function run(): void
    {
        foreach ($this->fieldGroups as $group) {
            if ($group instanceof FieldGroupInterface) {
                $group->setMaybeCacheFn(
                    function (string $class, callable $fn) {
                        if ($this->enableCache) {
                            $key = self::CACHE_PREFIX.str_replace('\\', '_', $class);

                            return $this->cache->get($key, $fn);
                        }

                        return $fn();
                    }
                );
                $this->wp->addFilter('init', fn () => $group->register(), -100);
            }
        }
    }
}
