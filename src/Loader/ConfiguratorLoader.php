<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Loader;

use Djvue\WpAdminBundle\Configurator\ConfiguratorInterface;

final class ConfiguratorLoader implements ConfiguratorLoaderInterface
{
    public function __construct(
        /**
         * @var list<ConfiguratorInterface>
         */
        private iterable $configurators,
    ) {
    }

    public function load(): void
    {
        foreach ($this->configurators as $configurator) {
            $configurator->run();
        }
    }
}
