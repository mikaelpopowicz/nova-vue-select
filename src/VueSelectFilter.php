<?php

namespace Mikaelpopowicz\NovaVueSelect;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

abstract class VueSelectFilter extends Filter
{
    protected $resourceName;

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'vue-select-filter';

    /**
     * VueSelectFilter constructor.
     *
     * @param  string  $resource
     */
    public function __construct(string $resource)
    {
        $this->resourceName = $resource;

        $this->withMeta([
            'resourceName' => $resource::uriKey(),
        ]);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [];
    }
}
