<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Helper;

use Symfony\Contracts\Cache\CacheInterface;

final class FieldOptions
{
    private const FIELDS_OPTIONS_CACHE_KEY = 'options_cache_key';
    private static ?array $cache = null;

    public function __construct(
        private CacheInterface  $fieldsOptionsCache,
    ) {}

    public function all(): array
    {
        if (self::$cache === null) {
            self::$cache = $this->fieldsOptionsCache->get(self::FIELDS_OPTIONS_CACHE_KEY, function () {
                $data = \get_fields('options');
                return $data;
            });
        }
        return self::$cache;
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

    public function getString(string $key): string
    {
        $res = $this->get($key);
        if (!is_string($res) && !is_int($res)) {
            return '';
        }
        return (string) $res;
    }

    public function getInt(string $key): int
    {
        $res = $this->get($key);
        if (is_array($res)) {
            return 0;
        }
        return (int) $res;
    }

    public function getArray(string $key): array
    {
        $data = $this->get($key);
        if (!is_array($data)) {
            return [];
        }
        return $data;
    }
}
