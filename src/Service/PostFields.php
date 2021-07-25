<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Helper;

use Djvue\WpAdminBundle\Service\WpFacade;
use Symfony\Contracts\Cache\CacheInterface;

final class PostFields
{
    private const POST_FIELDS_CACHE_KEY_PREFIX = 'post_fields_';
    private static array $cached = [];

    public function __construct(
        private bool $enableCache,
        private CacheInterface $cache,
        private WpFacade $wp,
    ) {
    }

    public function get(string $name, \WP_Post|int $postId): mixed
    {
        return $this->all($postId)[$name] ?? null;
    }

    public function all(\WP_Post|int $postId): array
    {
        $postId = (int) $postId;
        if (!isset(self::$cached[$postId])) {
            self::$cached[$postId] = $this->fetchValue($postId);
        }

        return self::$cached[$postId];
    }

    private function fetchValue(int $postId): array
    {
        $cb = fn () => $this->wp->getFields($postId);
        if ($this->enableCache) {
            return $this->cache->get(self::POST_FIELDS_CACHE_KEY_PREFIX.'_'.$postId, $cb);
        }

        return $cb();
    }

    public function clearCache(\WP_Post|int $postId): void
    {
        $this->cache->delete($this->getCacheKey($postId));
    }

    private function getCacheKey(\WP_Post|int|null $postId): string
    {
        $postId = (int) $postId;

        return self::POST_FIELDS_CACHE_KEY_PREFIX.'_'.$postId;
    }
}
