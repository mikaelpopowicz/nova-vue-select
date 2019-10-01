<?php

namespace Mikaelpopowicz\NovaVueSelect;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormatsRelatableDisplayValues;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class VueSelect extends Field
{
    use FormatsRelatableDisplayValues;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'vue-select';

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
    public $resourceIds;

    /**
     * The display values of the related Eloquent models.
     *
     * @var string
     */
    public $selectedResourcesDisplay;

    /**
     * The column that should be displayed for the field.
     *
     * @var \Closure
     */
    public $display;

    /**
     * @var boolean
     */
    public $isMultiple = false;

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

        $this->resourceIds = is_array($this->value) ? $this->value : [$this->value];
        $this->resourceIds = array_map(function ($id) {
            return (int) $id;
        }, $this->resourceIds);

        $this->selectedResourcesDisplay = collect($this->resourceIds)
            ->map(function ($id) {
                /** @var \Illuminate\Database\Eloquent\Model $model */
                $model = forward_static_call([$this->resourceClass, 'newModel']);
                $model = $model->newQuery()->find($id);

                return ! is_null($model)
                    ? [
                        'id' => $id,
                        'display' => $this->formatDisplayValue(new $this->resourceClass($model)),
                    ]
                    : null;
            })
            ->filter()
            ->toArray();
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return mixed
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            $model->{$attribute} = $this->isNullValue($value) ? null : ($this->isMultiple ? explode(',', $value) : $value);
        }
    }

    /**
     * @param  bool  $value
     * @return $this
     */
    public function multiple(bool $value = true)
    {
        $this->isMultiple = $value;

        return $this;
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
            'resourceIds' => $this->resourceIds,
            'selectedResourcesDisplay' => $this->selectedResourcesDisplay,
            'multiple' => $this->isMultiple,
        ], $this->meta);
    }
}
