<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Service;

use WP_Post;

final class WpFacade
{
    public function __construct(
        private string $host
    ) {
    }

    public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        \add_filter($hookName, $callback, $priority, $acceptedArgs);
    }

    public function removeFilter(string $hookName, callable $callback, int $priority = 10): void
    {
        \remove_filter($hookName, $callback, $priority);
    }

    public function removeAllFilters(string $hookName, ?int $priority = null): void
    {
        \remove_all_filters($hookName, $priority ?? false);
    }

    public function removeMenuPage(string $menuSlug): void
    {
        \remove_menu_page($menuSlug);
    }

    public function removeSubmenuPage(string $menuSlug, string $submenuSlug): void
    {
        \remove_submenu_page($menuSlug, $submenuSlug);
    }

    public function addImageSize(string $name, int $width = 0, int $height = 0, bool|array $crop = false): void
    {
        \add_image_size($name, $width, $height, $crop);
    }

    public function addMenuPage(
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $menuSlug,
        ?callable $function = null,
        string $iconUrl = '',
        ?int $position = null
    ): string {
        return \add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, $function, $iconUrl, $position);
    }

    public function addOptionsPage(array $data): void
    {
        if (!function_exists('acf_add_options_page')) {
            return;
        }
        \acf_add_options_page($data);
    }

    public function getField(string $name, mixed $postId, bool $formatValue = true): mixed
    {
        if (!function_exists('get_field')) {
            return null;
        }

        return \get_field($name, $postId, $formatValue);
    }

    public function getFields(mixed $postId, bool $formatValue = true): array
    {
        $data = \get_fields($postId, $formatValue);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    public function updateField(string $selector, mixed $value, mixed $postId): bool
    {
        if (!function_exists('update_field')) {
            return false;
        }

        return \update_field($selector, $value, $postId);
    }

    public function getOption(string $option, mixed $default = false): mixed
    {
        return \get_option($option, $default);
    }

    public function updateOption(string $option, mixed $value): bool
    {
        return \update_option($option, $value, false);
    }

    public function getPostBySlug(string $slug, string $postType = 'post'): ?WP_Post
    {
        [$posts] = $this->query(
            [
                'name' => $slug,
                'post_type' => $postType,
                'post_status' => 'publish',
                'numberposts' => 1,
            ]
        );

        return $posts[0] ?? null;
    }

    /**
     * @psalm-return array{0: list<\WP_Post>, 1: int}
     */
    public function query(array $data): array
    {
        $wpQuery = new \WP_Query($data);
        $posts = $wpQuery->posts;
        $total = $wpQuery->found_posts;

        return [$posts, $total];
    }

    public function getPageByPath(string $path): ?WP_Post
    {
        return \get_page_by_path($path);
    }

    public function getTemplateNameByPost(WP_Post $post): string
    {
        return \get_page_template_slug($post);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPostPath(WP_Post|int $post): string
    {
        return $this->getPath($this->getPostUrl($post));
    }

    public function getPath(string $url): string
    {
        return str_replace($this->host, '', $url);
    }

    public function getPostUrl(WP_Post|int $post): string
    {
        return \get_permalink($post);
    }

    public function getPostById(int $postId): ?WP_Post
    {
        return \get_post($postId);
    }

    public function getMainPost(): ?WP_Post
    {
        $mainPageId = (int) get_option('page_on_front');

        return $this->getPostById($mainPageId);
    }

    public function getPostMeta(int $postId, string $key, bool $isSingle = true): mixed
    {
        return \get_post_meta($postId, $key, $isSingle);
    }

    public function updatePostMeta(int $postId, string $key, mixed $value): void
    {
        \update_post_meta($postId, $key, $value);
    }
}
