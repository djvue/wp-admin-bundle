<?php

namespace Djvue\WpAdminBundle\FieldGroup;

use Djvue\WpAdminBundle\Interfaces\Registrable;
use StoutLogic\AcfBuilder\FieldsBuilder;

abstract class AbstractFieldGroup implements Registrable
{
    protected string $name;
    protected FieldsBuilder $builder;
    protected int $menuOrder = 10;

    public function __construct()
    {
    }

    public function register(): void
    {
        $this->builder = $this->createBuilder($this->name);
        $this->fields($this->builder);
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group($this->builder->build());
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
