<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Helper;

use Djvue\WpAdminBundle\Service\WpFacade;
use Symfony\Contracts\Cache\CacheInterface;

final class OptionFields
{
    private const FIELDS_OPTIONS_CACHE_KEY = 'option_fields';
    private static ?array $cached = null;

    public function __construct(
        private bool $enableCache,
        private CacheInterface $cache,
        private WpFacade $wp,
    ) {
    }

    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $item = $this->all();
        foreach ($keys as $subKey) {
            if (!isset($item[$subKey])) {
                return false;
            }
            $item = $item[$subKey];
        }

        return true;
    }

    public function all(): array
    {
        if (self::$cached === null) {
            self::$cached = $this->fetchValue();
        }

        return self::$cached;
    }

    private function fetchValue(): array
    {
        $cb = fn () => $this->wp->getFields('options');
        if ($this->enableCache) {
            return $this->cache->get(self::FIELDS_OPTIONS_CACHE_KEY, $cb);
        }

        return $cb();
    }

    public function getString(string $key): string
    {
        $res = $this->get($key);
        if (!is_string($res) && !is_int($res)) {
            return '';
        }

        return (string) $res;
    }

    public function get(string $key): mixed
    {
        $keys = explode('.', $key);
        $item = $this->all();
        foreach ($keys as $subKey) {
            $item = $item[$subKey] ?? null;
        }

        return $item;
    }

    public function getInt(string $key): int
    {
        $res = $this->get($key);
        if (is_array($res)) {
            return 0;
        }

        return (int) $res;
    }

    public function getBool(string $key): bool
    {
        return (bool) $this->get($key);
    }

    public function getArray(string $key): array
    {
        $data = $this->get($key);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    public function clearCache(): void
    {
        $this->cache->delete(self::FIELDS_OPTIONS_CACHE_KEY);
    }
}
