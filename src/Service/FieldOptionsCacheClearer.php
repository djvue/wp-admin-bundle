<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Service;

use App\Wp\Helper\FieldOptions;
use Psr\Cache\CacheItemPoolInterface;

final class FieldOptionsCacheClearer
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private FieldOptions $fieldOptions,
    ) {}

    public function clear(): void
    {
        $this->cache->clear();
    }

    public function refresh(): void
    {
        $this->clear();
        $this->fieldOptions->all();
    }
}
