<?php

namespace Djvue\WpAdminBundle\Configurator;

use Djvue\WpAdminBundle\Helper\DirectoryClassContainer;
use Djvue\WpAdminBundle\Interfaces\Registrable;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FieldGroupConfigurator extends AbstractConfigurator
{
    private ParameterBagInterface $parameterBag;
    private DirectoryClassContainer $directoryClassContainer;

    public function __construct(ParameterBagInterface $parameterBag, DirectoryClassContainer $directoryClassContainer)
    {
        $this->parameterBag = $parameterBag;
        $this->directoryClassContainer = $directoryClassContainer;
    }

    public function run(): void
    {
        $baseNamespace = $this->parameterBag->get('wp.namespaces.field_group');
        $fieldGroups = $this->directoryClassContainer->getClasses($baseNamespace);
        foreach ($fieldGroups as $group) {
            if ($group instanceof Registrable) {
                add_action('plugins_loaded', fn() => $group->register());
            }
        }
    }
}
