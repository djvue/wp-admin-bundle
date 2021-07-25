<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

use Djvue\WpAdminBundle\Service\WpFacade;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

class SlugTranslitConfigurator implements ConfiguratorInterface
{
    public function __construct(
        private WpFacade $wp,
    ) {
    }

    public function run(): void
    {
        $this->wp->addFilter('sanitize_title', [$this, 'sanitizeTitle'], 9, 3);
        $this->wp->addFilter('sanitize_file_name', [$this, 'sanitizeFileName'], 10, 3);
        $this->wp->addFilter('wp_insert_post_data', [$this, 'sanitizePostName'], 10, 2);
    }

    /**
     * Sanitize title.
     *
     * @param string $title Sanitized title.
     * @param string $raw_title The title prior to sanitization.
     * @param string $context The context for which the title is being sanitized.
     *
     * @return string
     */
    public function sanitizeTitle($title, $raw_title = '', $context = ''): string
    {
        global $wpdb;

        // var_dump($title, $context);

        // Fixed bug with `_wp_old_slug` redirect.
        if ('query' === $context) {
            return $title;
        }

        $title = urldecode($title);

        $is_term = false;
        // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
        $backtrace = debug_backtrace();
        // phpcs:enable
        foreach ($backtrace as $backtrace_entry) {
            if ('wp_insert_term' === $backtrace_entry['function']) {
                $is_term = true;
                break;
            }
        }

        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $term = $is_term ? $wpdb->get_var($wpdb->prepare("SELECT slug FROM $wpdb->terms WHERE name = %s", $title)) : '';
        // phpcs:enable

        if (!empty($term)) {
            $title = $term;
        } else {
            $title = $this->translit($title);
        }

        return $title;
    }

    /**
     * Sanitize title.
     *
     * @param string $title Sanitized title.
     * @param string $raw_title The title prior to sanitization.
     * @param string $context The context for which the title is being sanitized.
     *
     * @return string
     */
    public function sanitizeFileName($title, $raw_title = '', $context = ''): string
    {
        global $wpdb;

        // var_dump($title, $context);

        // Fixed bug with `_wp_old_slug` redirect.
        if ('query' === $context) {
            return $title;
        }

        $title = urldecode($title);

        $is_term = false;
        // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
        $backtrace = debug_backtrace();
        // phpcs:enable
        foreach ($backtrace as $backtrace_entry) {
            if ('wp_insert_term' === $backtrace_entry['function']) {
                $is_term = true;
                break;
            }
        }

        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $term = $is_term ? $wpdb->get_var($wpdb->prepare("SELECT slug FROM $wpdb->terms WHERE name = %s", $title)) : '';
        // phpcs:enable

        if (!empty($term)) {
            $title = $term;
        } else {
            $title = $this->translitFileName($title);
        }

        return $title;
    }

    public function translit(string $s): string
    {
        $s = mb_strtolower($s);

        return (string) (new AsciiSlugger())->slug($s);
    }

    public function translitFileName(string $s): string
    {
        $s = mb_strtolower($s);
        $s = (string) (new UnicodeString($s))->ascii();
        $s = preg_replace('/[^a-z0-9-_]/', '-', $s);

        return $s;
    }

    /**
     * Helper function to make class unit-testable
     *
     * @param string $function Function name.
     *
     * @return bool
     */
    protected function functionExists($function)
    {
        return function_exists($function);
    }

    /**
     * Check if Classic Editor plugin is active.
     *
     * @link https://kagg.eu/how-to-catch-gutenberg/
     *
     * @return bool
     */
    private function isClassicEditorPluginActive(): bool
    {
        if (!$this->functionExists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active('classic-editor/classic-editor.php');
    }

    /**
     * Check if Block Editor is active.
     * Must only be used after plugins_loaded action is fired.
     *
     * @link https://kagg.eu/how-to-catch-gutenberg/
     *
     * @return bool
     */
    private function isGutenbergEditorActive(): bool
    {
        // Gutenberg plugin is installed and activated.
        $gutenberg = !(false === has_filter('replace_editor', 'gutenberg_init'));

        // Block editor since 5.0.
        $block_editor = version_compare($GLOBALS['wp_version'], '5.0-beta', '>');

        if (!$gutenberg && !$block_editor) {
            return false;
        }

        if ($this->isClassicEditorPluginActive()) {
            $editor_option = get_option('classic-editor-replace');
            $block_editor_active = array('no-replace', 'block');

            return in_array($editor_option, $block_editor_active, true);
        }

        return true;
    }

    /**
     * Gutenberg support
     *
     * @param array $data An array of slashed post data.
     * @param array $postarr An array of sanitized, but otherwise unmodified post data.
     *
     * @return mixed
     */
    public function sanitizePostName($data, $postarr = array())
    {
        global $current_screen;

        if (!$this->isGutenbergEditorActive()) {
            return $data;
        }

        // Run code only on post edit screen.
        if (!($current_screen && 'post' === $current_screen->base)) {
            return $data;
        }

        if (
            !$data['post_name'] && $data['post_title'] && !in_array($data['post_status'], array('auto-draft', 'revision'), true)
        ) {
            $data['post_name'] = sanitize_title($data['post_title']);
        }

        return $data;
    }
}
