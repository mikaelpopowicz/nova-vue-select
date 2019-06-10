<?php

namespace Mikaelpopowicz\NovaVueSelect;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormatsRelatableDisplayValues;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Laravel\Nova\TrashedStatus;

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
        ]);
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
     * Build an associatable query for the field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  bool  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAssociatableQuery(NovaRequest $request, $withTrashed = false)
    {
        $model = forward_static_call(
            [$resourceClass = $this->resourceClass, 'newModel']
        );

        $query = $request->first === 'true'
            ? $model->newQueryWithoutScopes()->whereKey($request->current)
            : $resourceClass::buildIndexQuery(
                $request, $model->newQuery(), $request->search,
                [], [], TrashedStatus::fromBoolean($withTrashed)
            );

        return $query->tap(function ($query) use ($request, $model) {
            forward_static_call($this->associatableQueryCallable($request, $model), $request, $query);
        });
    }

    /**
     * Get the associatable query method name.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    protected function associatableQueryCallable(NovaRequest $request, $model)
    {
        return ($method = $this->associatableQueryMethod($request, $model))
            ? [$request->resource(), $method]
            : [$this->resourceClass, 'relatableQuery'];
    }

    /**
     * Get the associatable query method name.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    protected function associatableQueryMethod(NovaRequest $request, $model)
    {
        $method = 'relatable'.Str::plural(class_basename($model));

        if (method_exists($request->resource(), $method)) {
            return $method;
        }
    }

    /**
     * Format the given associatable resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return array
     */
    public function formatAssociatableResource(NovaRequest $request, $resource)
    {
        return array_filter([
            'display' => $this->formatDisplayValue($resource),
            'value' => $resource->getKey(),
        ]);
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
