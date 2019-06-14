<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <multiselect
                    v-if="this.field.resourceName"
                    :id="field.name"
                    :dusk="field.attribute"
                    v-model="selectedResource"
                    :options="availableResources"
                    :disabled="isReadonly"
                    :loading="isLoading"
                    label="display"
                    track-by="value"
                    @search-change="performSearch"
                    :class="errorClasses"
                    :placeholder="__('Search')"
                    :selectLabel="__('Press enter to select')"
                    :selectGroupLabel="__('Press enter to select group')"
                    :selectedLabel="__('Selected')"
                    :deselectLabel="__('Press enter to remove')"
                    :deselectGroupLabel="__('Press enter to deselect group')"
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
        </template>
    </default-field>
</template>

<script>
    import _ from 'lodash'
    import Multiselect from 'vue-multiselect'
    import storage from './../VueSelectFieldStorage'
    import { FormField, HandlesValidationErrors, PerformsSearches } from 'laravel-nova'

    export default {
        mixins: [FormField, HandlesValidationErrors, PerformsSearches],

        components: {
            Multiselect
        },

        props: ['resourceName', 'resourceId', 'field'],

        data: () => ({
            isLoading: false,
            availableResources: [],
            initializingWithExistingResource: false,
            selectedResource: null,
            selectedResourceId: null,
            softDeletes: false,
            withTrashed: false,
            search: '',
        }),

        /**
         * Mount the component.
         */
        mounted() {
            this.initializeComponent()
        },

        methods: {
            initializeComponent() {
                if (this.field.resourceName) {
                    this.withTrashed = false
                    if (this.editingExistingResource) {
                        this.initializingWithExistingResource = true
                        this.selectedResourceId = this.field.resourceId

                        this.getAvailableResources().then(() => this.selectInitialResource())
                    }

                    this.determineIfSoftDeletes()
                }
            },

            /**
             * Fill the forms formData with details from this field
             */
            fill(formData) {
                formData.append(
                    this.field.attribute,
                    this.selectedResource ? this.selectedResource.value : ''
                )

                formData.append(this.field.attribute + '_trashed', this.withTrashed)
            },

            /**
             * Get the resources that may be related to this resource.
             */
            getAvailableResources() {
                this.isLoading = true
                return storage
                    .fetchAvailableResources(this.field.resourceName, this.queryParams)
                    .then(({ data: { resources, softDeletes, withTrashed } }) => {
                        if (this.initializingWithExistingResource) {
                            this.withTrashed = withTrashed
                        }

                        // Turn off initializing the existing resource after the first time
                        this.initializingWithExistingResource = false
                        this.availableResources = resources
                        this.softDeletes = softDeletes
                        this.isLoading = false
                    })
            },

            /**
             * Determine if the relatd resource is soft deleting.
             */
            determineIfSoftDeletes() {
                return storage.determineIfSoftDeletes(this.field.resourceName).then(response => {
                    this.softDeletes = response.data.softDeletes
                })
            },

            /**
             * Select the initial selected resource
             */
            selectInitialResource() {
                this.selectedResource = _.find(
                    this.availableResources,
                    r => r.value == this.selectedResourceId
                )
            },

            /**
             * Update the field's internal value.
             */
            handleChange(item) {
                this.selectedResourceId = item.value || ''
                this.selectInitialResource()
            },

            /**
             * Perform a search to get the relatable resources.
             */
            performSearch(search) {
                this.isLoading = true
                this.search = search

                const trimmedSearch = search.trim()
                // If the user performs an empty search, it will load all the results
                // so let's just set the availableResources to an empty array to avoid
                // loading a huge result set
                if (trimmedSearch == '') {
                    this.clearSelection()

                    return
                }

                this.debouncer(() => {
                    this.selectedResource = ''
                    this.getAvailableResources(trimmedSearch)
                }, 500)
            },

            /**
             * Clear the selected resource and availableResources
             */
            clearSelection() {
                this.isLoading = false
            },
        },

        computed: {
            /**
             * Determine if we are editing an existing resource
             */
            editingExistingResource() {
                return Boolean(this.field.resourceId)
            },

            /**
             * Get the query params for getting available resources
             */
            queryParams() {
                return {
                    params: {
                        current: this.selectedResourceId,
                        first: this.initializingWithExistingResource,
                        search: this.search,
                        withTrashed: this.withTrashed,
                    },
                }
            },
        }
    }
</script>
