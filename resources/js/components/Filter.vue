<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>

        <div class="p-2">
            <multiselect
                v-if="this.filter.resourceName"
                :dusk="filter.name + '-filter-select'"
                v-model="selectedResource"
                :options="availableResources"
                :loading="isLoading"
                label="display"
                track-by="value"
                @search-change="performSearch"
                @input="handleChange"
                :placeholder="__('Search')"
                selectLabel=""
                selectGroupLabel=""
                selectedLabel=""
                deselectLabel=""
                deselectGroupLabel=""
                :internal-search="false"
            >

                <template slot="beforeList" v-if="isLoading && !availableResources.length">
                    <span class="multiselect__option">
                        <loader with="30" />
                    </span>
                </template>
                <template slot="noResult">{{ this.__('No result') }}</template>
                <template slot="noOptions">{{ this.__('No options') }}</template>
            </multiselect>
            <p v-else class="text-danger">{{ __('Resource is not defined') }}</p>
        </div>
    </div>
</template>

<script>
    import { PerformsSearches } from 'laravel-nova'
    import Multiselect from 'vue-multiselect'
    import Selectable from './Selectable'

    export default {
        mixins: [PerformsSearches, Selectable],

        components: {
            Multiselect
        },

        props: {
            resourceName: {
                type: String,
                required: true,
            },
            filterKey: {
                type: String,
                required: true,
            },
        },

        /**
         * Mount the component.
         */
        mounted() {
            this.initializeComponent()
        },

        methods: {
            initializeComponent() {
                if (this.value !== null && this.value !== undefined && this.value !== '') {
                    this.withTrashed = false
                    this.initializingWithExistingResources = true
                    this.selectedResourceId = this.value

                    this.getAvailableResources().then(() => this.selectInitialResource())
                    this.determineIfSoftDeletes()
                }
            },

            handleChange(event) {
                this.$store.commit(`${this.resourceName}/updateFilterState`, {
                    filterClass: this.filterKey,
                    value: event && event.value || '',
                })

                this.$emit('change')
            },
        },

        computed: {
            filter() {
                return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey)
            },

            value() {
                return this.filter.currentValue
            },

            searchableResourceName() {
                return this.filter.resourceName
            }
        },
    }
</script>
