<?php

namespace Djvue\WpAdminBundle\FieldGroup;

interface FieldGroupInterface
{
    public function setMaybeCacheFn(callable $fn): void;

    public function register(): void;
}
