<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

interface ConfiguratorInterface
{
    public function run(): void;
}
