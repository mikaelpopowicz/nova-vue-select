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
                    :multiple="isMultiple"
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
    import { FormField, HandlesValidationErrors, PerformsSearches } from 'laravel-nova'
    import Multiselect from 'vue-multiselect'
    import Selectable from './Selectable'

    export default {
        mixins: [FormField, HandlesValidationErrors, PerformsSearches, Selectable],

        components: {
            Multiselect
        },

        props: ['resourceName', 'resourceIds', 'field'],

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
                        this.initializingWithExistingResources = true
                        this.selectedResourcesIds = this.field.resourceIds

                        this.getAvailableResources().then(() => this.selectInitialResources())
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
                    this.selectedResources.map(r => r.value)
                )

                formData.append(this.field.attribute + '_trashed', this.withTrashed)
            },

            /**
             * Update the field's internal value.
             */
            handleChange(item) {
                this.selectedResourcesIds = item.value || ''
                this.selectInitialResources()
            },
        },

        computed: {
            /**
             * Determine if we are editing an existing resource
             */
            editingExistingResource() {
                return Boolean(this.field.resourceIds.length)
            }
        }
    }
</script>
