<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Loader;

interface LoaderInterface
{
    public function loadCore(bool $ignoreNoConsole = false): void;
}
