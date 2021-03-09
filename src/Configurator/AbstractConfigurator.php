<?php

namespace Djvue\WpAdminBundle\Configurator;

use Djvue\WpAdminBundle\Interfaces\Runnable;

abstract class AbstractConfigurator implements Runnable
{
    abstract public function run(): void;
}
