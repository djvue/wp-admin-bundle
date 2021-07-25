<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Loader;

interface ConfiguratorLoaderInterface
{
    public function load(): void;
}
