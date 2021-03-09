<?php

namespace Djvue\WpAdminBundle\Helper;

class Fields
{
    private const OPTIONS_SELECTOR = 'options';

    public function getOption(string $name, $default = null)
    {
        return $this->get($name, self::OPTIONS_SELECTOR, $default);
    }

    public function get(string $name, string $selector, $default = null)
    {
        $value = get_field($name, $selector);
        if (null === $default) {
            return $value;
        }

        return $value === false ? $default : $value;
    }
}
