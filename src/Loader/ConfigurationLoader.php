<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Loader;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getHost(): string
    {
        return $this->params->get('wp_admin.host');
    }

    public function getRootDir(): string
    {
        return $this->params->get('kernel.project_dir');
    }

    private function getConfigParameter(string $key, $default = null, $prefix = 'wp_admin.')
    {
        $fullKey = $prefix.$key;

        return $this->params->has($fullKey) ? $this->params->get($fullKey) : $default;
    }

    public function getTablePrefix(): string
    {
        return $this->getConfigParameter('table_prefix', 'wp_');
    }

    public function load(): void
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            set_time_limit(600);
        }
        // define('WPMU_PLUGIN_DIR', dirname(__DIR__) . '/MuPlugins');

        $_SERVER['HTTPS'] = str_contains($this->getHost(), 'https://') ? 'on' : 'off';
        // disable wp core redirects
        // define('SHORTINIT', true);

        define('WP_DISABLE_FATAL_ERROR_HANDLER', true);

        define('WP_HOME', $this->getHost());
        define('WP_SITEURL', $this->getHost() . '/' . trim($this->getConfigParameter('site_path', 'wp'), '/'));
        define('WP_CONTENT_URL', $this->getHost() . '/' . trim($this->getConfigParameter('content_path', 'content'), '/'));
        define('COOKIEPATH', '/');

        if (!defined('ABSPATH')) {
            define(
                'ABSPATH',
                $this->getRootDir().DIRECTORY_SEPARATOR
                .trim(
                    $this->getConfigParameter('wp_admin.root_directory', 'public/wp'),
                    DIRECTORY_SEPARATOR
                ).DIRECTORY_SEPARATOR
            );
        }
        define(
            'WP_CONTENT_DIR',
            $this->getRootDir().DIRECTORY_SEPARATOR
            .trim($this->getConfigParameter('wp_admin.content_directory', 'public/content'), DIRECTORY_SEPARATOR)
        );
        define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

        // db config
        $dbConfig = $this->getConfigParameter('database');
        define('DB_NAME', $dbConfig['name'] ?? '');
        define('DB_USER', $dbConfig['user'] ?? '');
        define('DB_PASSWORD', $dbConfig['password'] ?? '');
        define('DB_HOST', ($dbConfig['host'] ?? '') . ':' . ($dbConfig['port'] ?? ''));

        define('DB_TABLE_PREFIX', $this->getTablePrefix());
        define('USE_MYSQL', true);
        define('DB_CHARSET', 'utf8');
        define('DB_COLLATE', '');

        define('WP_USE_THEMES', false);
        define('WP_DEFAULT_THEME', 'custom');
        // Allow WP edit files by php
        define('FS_METHOD', 'direct');
        define('WPLANG', 'ru_RU');

        $salt = $this->getConfigParameter('secret', '', 'framework');
        define('AUTH_KEY', $salt);
        define('SECURE_AUTH_KEY', $salt);
        define('LOGGED_IN_KEY', $salt);
        define('NONCE_KEY', $salt);
        define('AUTH_SALT', $salt);
        define('SECURE_AUTH_SALT', $salt);
        define('LOGGED_IN_SALT', $salt);
        define('NONCE_SALT', $salt);


        $debug = $this->getConfigParameter('debug', false, 'kernel');
        define('WP_DEBUG', $debug);
        define('SAVEQUERIES', $this->getConfigParameter('save_queries', false));
        // %%WP_STAGE%% //'production'
        define('WP_STAGE', $this->getConfigParameter('wp_admin.env', 'production'));
        define('STAGING_DOMAIN', '%%WP_STAGING_DOMAIN%%');
    }
}
