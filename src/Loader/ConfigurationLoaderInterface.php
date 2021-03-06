<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Loader;

interface ConfigurationLoaderInterface
{
    public function getTablePrefix(): string;
    public function load(): void;
}
