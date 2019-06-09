<?php

namespace Mikaelpopowicz\NovaVueSelect;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
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
     * @var string
     */
    protected $resourceClass;

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

        $this->resourceClass = $resource;

        return $this->withMeta([
            'resource' => $resource::uriKey(),
            'resourceAttribute' => $resource::$title,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        if ($this->resourceClass && $this->value) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model = $this->resourceClass::newModel()->find($this->value);

            if ($model) {
                /** @var \Laravel\Nova\Resource $resource */
                $resource = new $this->resourceClass($model);
                $this->withMeta([
                    'resourceValue' => $resource->title(),
                ]);
            }
        }
    }
}
