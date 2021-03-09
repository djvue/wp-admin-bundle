<?php

use Djvue\WpAdminBundle\WpAdminBundle;

try {
    $loader = WpAdminBundle::getLoader();
    $loader->loadMuPlugins();
} catch (\Throwable $exception) {
    dump($exception);
}
