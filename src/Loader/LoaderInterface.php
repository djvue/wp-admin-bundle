<?php


namespace Djvue\WpAdminBundle\Loader;


interface LoaderInterface
{
    public function loadCore(bool $ignoreNoConsole): void;
}
