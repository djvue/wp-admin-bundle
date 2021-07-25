<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

class HostConfigurator implements ConfiguratorInterface
{
    public function __construct(
        private string $host,
    ) {
    }

    public function run(): void
    {
        force_ssl_admin(str_contains($this->host, 'https://'));
        add_filter(
            'site_url',
            function ($url) {
                // var_dump($url . ' -> ' . preg_replace('/^https?:\/\/[^\/]+\/(.+)$/', $host . '/$1', $url));
                return preg_replace('/^https?:\/\/[^\/]+\/(.+)$/', $this->host.'/$1', $url);
            },
            10,
            1
        );
    }
}
