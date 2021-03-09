<?php

namespace Djvue\WpAdminBundle\Loader;

use Djvue\WpAdminBundle\Configurator\ConfigureHooksConfigurator;
use Djvue\WpAdminBundle\Configurator\FieldGroupConfigurator;
use Djvue\WpAdminBundle\Configurator\HostConfigurator;
use Djvue\WpAdminBundle\Configurator\PageTemplatesConfigurator;
use Djvue\WpAdminBundle\Configurator\SlugTranslitConfigurator;
use Djvue\WpAdminBundle\Helper\DirectoryClassContainer;
use Djvue\WpAdminBundle\Interfaces\Runnable;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Loader implements LoaderInterface
{
    private ConfigurationLoaderInterface $configurationLoader;
    private DirectoryClassContainer $directoryClassContainer;
    private ParameterBagInterface $parameterBag;
    /**
     * @var Runnable[] $configurators
     */
    private array $configurators;

    public function __construct(
        ConfigurationLoaderInterface $configurationLoader,
        DirectoryClassContainer $directoryClassContainer,
        ParameterBagInterface $parameterBag,

        // Configurators
        FieldGroupConfigurator $fieldGroupConfigurator,
        HostConfigurator $hostConfigurator,
        PageTemplatesConfigurator $pageTemplatesConfigurator,
        SlugTranslitConfigurator $slugTranslitConfigurator,
        ConfigureHooksConfigurator $configureHooksConfigurator
    )
    {
        $this->configurationLoader = $configurationLoader;
        $this->directoryClassContainer = $directoryClassContainer;
        $this->parameterBag = $parameterBag;
        $this->configurators = [
            $fieldGroupConfigurator,
            $hostConfigurator,
            $pageTemplatesConfigurator,
            $slugTranslitConfigurator,
            $configureHooksConfigurator
        ];
    }

    public function loadCore(): void
    {
        if (!$this->isLoaded() && !$this->isConsole()) {
            $this->configurationLoader->load();
            /** @psalm-suppress UndefinedConstant */
            require_once ABSPATH.'/wp-includes/plugin.php';
            /** @psalm-suppress UndefinedFunction */
            add_action('muplugins_loaded', fn() => $this->runConfigurators());
            $this->doLoadCore();
            // $this->terminate();
        }
    }

    protected function isLoaded(): bool
    {
        return function_exists('wp_get_server_protocol');
    }

    protected function isConsole(): bool
    {
        return $_SERVER['SCRIPT_NAME'] === 'bin/console';
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

        do_action_ref_array( 'wp', array( &$wp ) );

        if ( ! isset( $wp_the_query ) ) {
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
            dump($error);
            die();
            // throw $error;
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

    protected function runConfigurators(): void
    {
        $baseNamespace = $this->parameterBag->get('wp.namespaces.configurator');
        $configurators = $this->directoryClassContainer->getClasses($baseNamespace);
        $configurators = [...$this->configurators, ...$configurators];
        foreach ($configurators as $configurator) {
            if ($configurator instanceof Runnable) {
                $configurator->run();
            }
        }
    }
}
