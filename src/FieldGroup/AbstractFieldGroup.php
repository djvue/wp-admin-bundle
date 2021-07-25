<?php

namespace Djvue\WpAdminBundle\FieldGroup;

use Closure;
use StoutLogic\AcfBuilder\FieldsBuilder;

abstract class AbstractFieldGroup implements FieldGroupInterface
{
    protected string $name;
    protected FieldsBuilder $builder;
    protected int $menuOrder = 10;
    protected ?Closure $maybeCacheFn = null;

    public function __construct()
    {
    }

    public function setMaybeCacheFn(callable $fn): void
    {
        $this->maybeCacheFn = Closure::fromCallable($fn);
    }

    public function register(): void
    {
        if (function_exists('acf_add_local_field_group')) {
            $fn = function () {
                $this->builder = $this->createBuilder($this->name);
                $this->fields($this->builder);

                return $this->builder->build();
            };
            $maybeCacheFn = $this->maybeCacheFn;
            if ($maybeCacheFn !== null) {
                $fields = $maybeCacheFn(static::class, $fn);
            } else {
                $fields = $fn();
            }
            acf_add_local_field_group($fields);
        }
    }

    protected function createBuilder(string $name): FieldsBuilder
    {
        $builder = new FieldsBuilder($name, [
            'menu_order' => $this->menuOrder
        ]);

        return $this->prepareBuilder($builder);
    }

    protected function prepareBuilder(FieldsBuilder $builder): FieldsBuilder
    {
        $builder->setGroupConfig('hide_on_screen', [
            'the_content',
            'excerpt',
            'discussion',
            'comments',
            'featured_image',
            'tags',
            'send-trackbacks',
        ]);

        return $builder;
    }

    abstract protected function fields(FieldsBuilder $builder): void;
}
