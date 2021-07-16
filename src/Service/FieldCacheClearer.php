<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Service;

use Djvue\WpAdminBundle\Configurator\FieldGroupConfigurator;
use Psr\Cache\CacheItemPoolInterface;

final class FieldCacheClearer
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {
    }

    public function clear(): void
    {
        $this->cache->clear(FieldGroupConfigurator::CACHE_PREFIX);
    }
}
