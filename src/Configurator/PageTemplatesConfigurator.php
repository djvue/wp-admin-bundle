<?php

namespace Djvue\WpAdminBundle\Configurator;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        $templatesPathDirectory = $this->parameterBag->get('wp_admin.page_templates_path');
        if ($templatesPathDirectory !== null) {
            $templatesPath = $this->parameterBag->get('twig.default_path').DIRECTORY_SEPARATOR.$templatesPathDirectory;
            $finder = (new Finder())->in($templatesPath);
            $files = iterator_to_array($finder);
            $keys = array_map(fn(SplFileInfo $file) => $this->resolveKey($file), $files);
            $names = array_map(fn(SplFileInfo $file) => $this->resolveTemplate($file), $files);
            $arr = array_merge($arr, array_combine($keys, $names));
        }

        return $arr;
    }

    private function resolveKey(SplFileInfo $file)
    {
        return str_replace('.html.twig', '', $file->getBasename());
    }

    private function resolveTemplate(SplFileInfo $file)
    {
        $content = $file->getContents();
        if (preg_match('/{# ?template:? ?["\']([^"\']+)["\'] ?#}/', $content, $matches)) {
            return $matches[1];
        }
        return $this->resolveKey($file);
    }
}
