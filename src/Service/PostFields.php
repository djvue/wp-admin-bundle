<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Service;

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
        $postId = $this->getPostId($postId);
        return $this->all($postId)[$name] ?? null;
    }

    public function all(\WP_Post|int $postId): array
    {
        $postId = $this->getPostId($postId);
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
        $postId = $this->getPostId($postId);
        $this->cache->delete($this->getCacheKey($postId));
    }

    public function refresh(\WP_Post|int $postId): void
    {
        $postId = $this->getPostId($postId);
        $this->clearCache($postId);
        $this->all($postId);
    }

    private function getCacheKey(\WP_Post|int|null $postId): string
    {
        $postId = $this->getPostId($postId);

        return self::POST_FIELDS_CACHE_KEY_PREFIX.'_'.$postId;
    }

    private function getPostId(\WP_Post|int|null $postId): int
    {
        if ($postId instanceof \WP_Post) {
            $postId = $postId->ID;
        }

        return (int) $postId;
    }
}
