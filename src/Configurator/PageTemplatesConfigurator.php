<?php

namespace Djvue\WpAdminBundle\Configurator;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PageTemplatesConfigurator extends AbstractConfigurator
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
        $templatesPath = $this->parameterBag->get('twig.default_path') . DIRECTORY_SEPARATOR . $this->parameterBag->get('wp.page_templates_path');
        $finder = (new Finder())->in($templatesPath);
        $files = iterator_to_array($finder);
        $keys = array_map(fn(SplFileInfo $file) => $this->resolveKey($file), $files);
        $names = array_map(fn(SplFileInfo $file) => $this->resolveTemplate($file), $files);
        return array_combine($keys, $names);
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
