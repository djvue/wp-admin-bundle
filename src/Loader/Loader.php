<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Loader;

use Djvue\WpAdminBundle\Service\WpFacade;

class Loader implements LoaderInterface
{
    public function __construct(
        private ConfigurationLoaderInterface $configurationLoader,
        private ConfiguratorLoaderInterface $configuratorLoader,
        private WpFacade $wp,
    ) {
    }

    public function loadCore(bool $ignoreNoConsole = false): void
    {
        if (!$this->isLoaded() && ($ignoreNoConsole || !$this->isConsole())) {
            $this->configurationLoader->load();
            $this->requireCore();
            $this->wp->addFilter('muplugins_loaded', fn () => $this->configuratorLoader->load());
            $this->doLoadCore();
            // $this->terminate();
        }
    }

    protected function requireCore(): void
    {
        /** @psalm-suppress UndefinedConstant */
        require_once ABSPATH.'/wp-includes/plugin.php';
    }

    protected function isLoaded(): bool
    {
        return function_exists('wp_get_server_protocol');
    }

    protected function isConsole(): bool
    {
        return str_contains($_SERVER['SCRIPT_NAME'], 'bin/console');
    }

    public function terminate(): void
    {
        $query = '';
        // ob_start();
        global $wp, $wp_query, $wp_the_query, $post;

        // need to prevent register globals
        // $wp->main( $query_vars );
        /**
         * \Wp $wp
         */
//
//        $pagePath = $_SERVER['REQUEST_URI'];
//        wp_cache_get_last_changed('posts');
//        $hash      = md5($pagePath . serialize($post_type));
//        $cache_key = "get_page_by_path:$hash:$last_changed";
//        wp_cache_set( $cache_key, 'posts' );
        /*add_action('parse_query', function (&$wpQuery) {
            $wpQuery->
        });*/
        if ($post && !in_array($post->post_type, ['page', 'attachment'])) {
            $query = [
                'p' => $post->ID
            ];
        }
        $wp->init();
        $wp->parse_request( $query );
        // $wp->send_headers();
        $wp->query_posts();
        // $wp->handle_404();

        // $wp->register_globals(); replaced below
        // <<register globals
        /*
        foreach ( (array) $wp_query->query_vars as $key => $value ) {
            $GLOBALS[ $key ] = $value;
        }
        $GLOBALS['query_string'] = $this->query_string;
        $GLOBALS['posts']        = & $wp_query->posts;
        $GLOBALS['post']         = isset( $wp_query->post ) ? $wp_query->post : null;
        // break symfony profiler
        // $GLOBALS['request']      = $wp_query->request;
        if ( $wp_query->is_single() || $wp_query->is_page() ) {
            $GLOBALS['more']   = 1;
            $GLOBALS['single'] = 1;
        }

        if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
            $GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
        }
        */
        // register globals>>

        if (function_exists('do_action_ref_array')) {
            do_action_ref_array('wp', [&$wp]);
        }

        if (!isset($wp_the_query)) {
            $wp_the_query = $wp_query;
        }
    }

    private function doLoadCore(): void
    {
        try {
            $this->fixGlobals();
            $table_prefix = $this->configurationLoader->getTablePrefix();
            require ABSPATH . '/wp-includes/class-requests.php';
            spl_autoload_register(['Requests', 'autoloader'], true, true);
            require_once(ABSPATH . 'wp-settings.php');
        } catch (\Throwable $error) {
            //            dump($error);
            //            die();
            throw $error;
        }
    }

    protected function fixGlobals(): void
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = '';
        }
        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = '/';
        }
    }
}
