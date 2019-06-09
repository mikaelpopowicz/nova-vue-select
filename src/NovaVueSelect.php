<?php

namespace Mikaelpopowicz\NovaVueSelect;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Resource;

class NovaVueSelect extends Select
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-vue-select';

    /**
     * Set the options for the select menu.
     *
     * @param  array  $options
     * @return $this
     */
    public function options($options)
    {
        return $this->withMeta([
            'options' => collect($options ?? [])->map(function ($label, $value) {
                return is_array($label) ? $label + ['value' => $value] : ['label' => $label, 'value' => $value];
            })->values()->all(),
        ]);
    }

    /**
     * Use resource as option.
     *
     * @param  string  $resource
     * @return \Mikaelpopowicz\NovaVueSelect\NovaVueSelect
     */
    public function resource(string $resource)
    {
        if (! is_subclass_of($resource, Resource::class)) {
            throw new \InvalidArgumentException($resource . ' is not a Nova resource');
        }

        return $this->withMeta([
            'resource' => $resource::uriKey(),
            'resourceTitle' => $resource::$title,
        ]);
    }
}
