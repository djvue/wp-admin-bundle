<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Configurator;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HostConfigurator implements ConfiguratorInterface
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function run(): void
    {
        $host = $this->parameterBag->get('wp_admin.host');
        force_ssl_admin(str_contains($host, 'https://'));
        add_filter('site_url', function ($url) use ($host) {
            // var_dump($url . ' -> ' . preg_replace('/^https?:\/\/[^\/]+\/(.+)$/', $host . '/$1', $url));
            return preg_replace('/^https?:\/\/[^\/]+\/(.+)$/', $host . '/$1', $url);
        }, 10, 1);
    }
}
