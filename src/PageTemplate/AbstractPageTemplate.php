<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\PageTemplate;

abstract class AbstractPageTemplate
{
    protected string $template = '';

    abstract public function transformData($data, \WP_Post $page): array;

    public function supportTemplate(string $templateName): bool
    {
        return $templateName === $this->template;
    }
}
