<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PageTemplatesConfigurator implements ConfiguratorInterface
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function run(): void
    {
        $templates = $this->getTemplates();
        add_filter('theme_page_templates', fn() => $templates, 10, 0);
    }

    private function getTemplates(): array
    {
        $arr = [];
        $templates = $this->parameterBag->get('wp_admin.page_templates');
        if (is_array($templates)) {
            $keys = array_map(static fn(array $template) => $template['key'] ?? $template['name'] ?? '', $templates);
            $names = array_map(static fn(array $template) => $template['name'] ?? $template['key'] ?? '', $templates);
            $arr = array_merge($arr, array_combine($keys, $names));
        }

        return $arr;
    }
}
