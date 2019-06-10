<?php

namespace Mikaelpopowicz\NovaVueSelect;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormatsRelatableDisplayValues;
use Laravel\Nova\Resource;

class NovaVueSelect extends Field
{
    use FormatsRelatableDisplayValues;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-vue-select';

    /**
     * The class name of the related resource.
     *
     * @var string
     */
    public $resourceClass;

    /**
     * The URI key of the related resource.
     *
     * @var string
     */
    public $resourceName;

    /**
     * The key of the related Eloquent model.
     *
     * @var string
     */
    public $resourceId;

    /**
     * The display value of the related Eloquent model.
     *
     * @var string
     */
    public $selectedResourceDisplay;

    /**
     * The column that should be displayed for the field.
     *
     * @var \Closure
     */
    public $display;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $resource
     * @return void
     */
    public function __construct($name, string $attribute, string $resource)
    {
        parent::__construct($name, $attribute);

        if (! is_subclass_of($resource, Resource::class)) {
            throw new \InvalidArgumentException($resource . ' is not a Nova resource');
        }

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $this->resourceId = $this->value;

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = forward_static_call([$this->resourceClass, 'newModel']);
        $model = $model->newQuery()->find($this->value);

        if ($model) {
            $resource = new $this->resourceClass($model);
            $this->selectedResourceDisplay = $this->formatDisplayValue($resource);
        }
    }

    /**
     * Get additional meta information to merge with the field payload.
     *
     * @return array
     */
    public function meta()
    {
        return array_merge([
            'resourceName' => $this->resourceName,
            'label' => forward_static_call([$this->resourceClass, 'label']),
            'singularLabel' => $this->singularLabel ?? $this->name ?? forward_static_call([$this->resourceClass, 'singularLabel']),
            'resourceId' => $this->resourceId,
            'selectedResourceDisplay' => $this->selectedResourceDisplay,
        ], $this->meta);
    }
}
