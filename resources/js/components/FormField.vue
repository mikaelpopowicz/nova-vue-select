<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <multiselect
                ref="input"
                v-if="this.field.resourceName"
                :id="field.name"
                :dusk="field.attribute"
                v-model="selectedResources"
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

        props: ['resourceName', 'field'],

        /**
         * Mount the component.
         */
        mounted() {
            this.removeOverflowHidden()
            this.initializeComponent()
        },

        methods: {
            initializeComponent() {
                this.isMultiple = this.field.multiple

                if (this.isMultiple) {
                    this.selectedResources = []
                    this.selectedResourcesIds = []
                }

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

            removeOverflowHidden() {
                let form = this.$refs.input.$el.closest('div[data-testid="confirm-action-modal"] form.overflow-hidden')

                if (form !== undefined && form !== null && form.classList.contains('overflow-hidden')) {
                    form.classList.remove('overflow-hidden')
                }
            },

            /**
             * Fill the forms formData with details from this field
             */
            fill(formData) {
                if (typeof this.selectedResources !== 'undefined' && this.selectedResources !== null) {
                    if (this.isMultiple && Array.isArray(this.selectedResources) && this.selectedResources.length) {
                        formData.append(this.field.attribute, this.selectedResources.map(r => r.value))
                    } else {
                        if (typeof this.selectedResources.value !== 'undefined' && this.selectedResources.value !== null) {
                            formData.append(this.field.attribute, this.selectedResources.value)
                        }
                    }

                    formData.append(this.field.attribute + '_trashed', this.withTrashed)
                }
            },
        },

        computed: {
            /**
             * Determine if we are editing an existing resource
             */
            editingExistingResource() {
                return Boolean(this.field.resourceIds.length)
            },

            isReadonly() {
                return this.field.readonly || _.get(this.field, 'extraAttributes.readonly')
            },
        }
    }
</script>
