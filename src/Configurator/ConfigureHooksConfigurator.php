<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

use Djvue\WpAdminBundle\Helper\OptionFields;
use Djvue\WpAdminBundle\Helper\PostFields;
use Djvue\WpAdminBundle\Service\WpFacade;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConfigureHooksConfigurator implements ConfiguratorInterface
{
    private string $defaultTimezone;

    public function __construct(
        private WpFacade $wp,
        private OptionFields $optionFields,
        private PostFields $postFields,
        ParameterBagInterface $parameterBag,
    ) {
        $this->defaultTimezone = $parameterBag->get('wp_admin.default_timezone');
    }

    public function run(): void
    {
        date_default_timezone_set($this->defaultTimezone);
        $this->wp->addFilter(
            'init',
            function () {
                $this->configureWpHeadHooks();
                $this->configureWpFooterHooks();
            }
        );
    }

    private function configureWpHooks(): void
    {
        $this->wp->removeAllFilters('wp_maybe_load_widgets', 0);
        $this->wp->removeFilter('init', 'wp_widgets_init', 1);

        $this->wp->removeFilter('admin_menu', [$this, 'adminMenu']);

        $this->wp->removeFilter('init', [$this, 'registerPostTypes']);

        $this->wp->removeFilter('use_block_editor_for_post', '__return_false', 10);
        $this->wp->removeFilter('use_block_editor_for_post_type', '__return_false', 10);

        $this->wp->removeFilter('pre_option_permalink_structure', fn () => '/%postname%');
        $this->wp->removeFilter('pre_option_category_base', fn () => '/');
        $this->wp->removeFilter('pre_option_tag_base', fn () => '/tags');

        $this->wp->removeFilter('show_admin_bar', '__return_false');
        $this->wp->removeFilter('wp_headers', fn () => []);

        $this->wp->addFilter(
            'acf/update_value',
            function ($value, $postId, $field) {
                $this->optionFields->clearCache();

                return $value;
            },
            10,
            3
        );

        $this->wp->addFilter(
            'acf/update_value',
            function ($value, $postId, $field) {
                $this->postFields->clearCache($postId);

                return $value;
            },
            10,
            3
        );
    }

    private function configureWpHeadHooks(): void
    {
        global $wp_filter, $wp_widget_factory;

        $tag = 'wp_print_styles';
        /**
         * \WP_Hook $stylesHook
         */
        $stylesHook = $wp_filter[$tag];
        $stylesHook->remove_filter($tag, 'print_emoji_styles', 10);

        /**
         * \WP_Hook $hook
         */
        $tag = 'wp_head';
        $hook = $wp_filter[$tag];
        foreach ([
                     ['wp_resource_hints', 2],
                     ['feed_links', 2],
                     ['feed_links_extra', 3],
                     ['print_emoji_detection_script', 7],
                     ['rest_output_link_wp_head', 10],
                     ['rsd_link', 10],
                     ['wlwmanifest_link', 10],
                     ['locale_stylesheet', 10],
                     ['wp_generator', 10],
                     ['wp_shortlink_wp_head', 10],
                     ['_custom_logo_header_styles', 10],
                     ['wp_oembed_add_host_js', 10],
                     ['wp_oembed_add_discovery_links', 10],
                     ['wp_custom_css_cb', 101],

                     // ['wp_print_styles', 8],
                     // ['wp_print_head_scripts', 9],
                 ] as $removeData) {
            $hook->remove_filter($tag, $removeData[0], $removeData[1]);
        }
        if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
            $hook->remove_filter(
                $tag,
                [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'],
                10
            );
        }
        $this->wp->addFilter(
            'wp_enqueue_scripts',
            function () {
                global $wp_styles;
                $wp_styles->dequeue('wp-block-library');
                $wp_styles->dequeue('yoast-seo-adminbar');
            },
            9999
        );
    }

    private function configureWpFooterHooks(): void
    {
        global $wp_filter, $wp_widget_factory, $wp_styles;
        /**
         * \WP_Hook $hook
         */
        $tag = 'wp_footer';
        $hook = $wp_filter[$tag];
    }
}
