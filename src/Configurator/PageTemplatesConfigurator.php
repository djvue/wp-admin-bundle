<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

use Djvue\WpAdminBundle\Service\WpFacade;

class PageTemplatesConfigurator implements ConfiguratorInterface
{
    public function __construct(
        private array $pageTemplates,
        private WpFacade $wp,
    ) {
    }

    public function run(): void
    {
        $templates = $this->getTemplates();
        $this->wp->addFilter('theme_page_templates', fn () => $templates, 10, 0);
    }

    private function getTemplates(): array
    {
        $arr = [];
        if (is_array($this->pageTemplates)) {
            $keys = array_map(
                static fn (array $template) => $template['key'] ?? $template['name'] ?? '',
                $this->pageTemplates
            );
            $names = array_map(
                static fn (array $template) => $template['name'] ?? $template['key'] ?? '',
                $this->pageTemplates
            );
            $arr = array_merge($arr, array_combine($keys, $names));
        }

        return $arr;
    }
}
