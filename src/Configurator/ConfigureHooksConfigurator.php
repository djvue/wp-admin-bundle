<?php

namespace Djvue\WpAdminBundle\Configurator;

class ConfigureHooksConfigurator implements ConfiguratorInterface
{
    public function __construct()
    {
    }

    public function run(): void
    {
        add_action('init', function () {
            $this->configureWpHeadHooks();
            $this->configureWpFooterHooks();
        });
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
        add_action(
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
