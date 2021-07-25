<?php


namespace Djvue\WpAdminBundle\Interfaces;


interface Registrable
{
    public function setMaybeCacheFn(callable $fn): void;

    public function register(): void;
}
