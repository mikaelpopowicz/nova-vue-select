<?php

namespace Mikaelpopowicz\NovaVueSelect\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\FormatsRelatableDisplayValues;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\TrashedStatus;

class SelectController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, FormatsRelatableDisplayValues;

    /**
     * The column that should be displayed for the field.
     *
     * @var \Closure
     */
    public $display;

    /**
     * List the available related resources for a given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(NovaRequest $request)
    {
        $resource = $request->resource();

        $model = forward_static_call(
            [$resource, 'newModel']
        );

        $withTrashed = $this->shouldIncludeTrashed(
            $request, $resource, $model
        );

        return [
            'resources' => $this->buildSelectQuery($request, $resource, $model, $withTrashed)->get()
                ->mapInto($resource)
                ->filter->authorizedToAdd($request, $request->model())
                ->map(function ($resource) use ($request) {
                    return $this->formatAssociatableResource($request, $resource);
                })->sortBy('display')->values(),
            'softDeletes' => $resource::softDeletes(),
            'withTrashed' => $withTrashed,
        ];
    }

    /**
     * Build select query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $resource
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  bool  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildSelectQuery(NovaRequest $request, string $resource, Model $model, $withTrashed = false): Builder
    {
        return $request->first === 'true'
            ? $model->newQueryWithoutScopes()->whereKey($request->current)
            : $resource::buildIndexQuery(
                $request, $model->newQuery(), $request->search,
                [], [], TrashedStatus::fromBoolean($withTrashed)
            );
    }

    /**
     * Determine if the query should include trashed models.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $resource
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    protected function shouldIncludeTrashed(NovaRequest $request, string $resource, Model $model)
    {
        if ($request->withTrashed === 'true') {
            return true;
        }

        if ($request->current && empty($request->search) && $resource::softDeletes()) {
            $model = $model->newQueryWithoutScopes()->find($request->current);

            return $model ? $model->trashed() : false;
        }

        return false;
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
}
