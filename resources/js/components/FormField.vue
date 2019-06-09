<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <multiselect
                :id="field.name"
                :dusk="field.attribute"
                v-model="value"
                :options="values"
                :disabled="isReadonly"
                :loading="isLoading"
                label="label"
                track-by="value"
                :class="errorClasses"
                :placeholder="__('Choose an option')"
                :selectLabel="__('Press enter to select')"
                :selectGroupLabel="__('Press enter to select group')"
                :selectedLabel="__('Selected')"
                :deselectLabel="__('Press enter to remove')"
                :deselectGroupLabel="__('Press enter to deselect group')"
            />
        </template>
    </default-field>
</template>

<script>
import Multiselect from 'vue-multiselect'
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    components: {
        Multiselect
    },

    props: ['resourceName', 'resourceId', 'field'],

    data: function () {
        return {
            isLoading: false,
            values: this.field.options || [],
            resourceLabel: this.field.resourceTitle || 'title',
            resourceValue: 'id',
        }
    },

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        async setInitialValue() {
            if (this.field.resource) {
                await this.fetchResource()
            }

            let initial = _.find(this.values, o => o.value === parseInt(this.field.value))
            this.value = initial || null
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value && this.value.value || '')
        },

        /**
         * Update the field's internal value.
         */
        handleChange(item) {
            this.value = item && item.value || ''
        },

        async fetchResource() {
            this.isLoading = true
            let options = []
            let response = await Nova.request().get(`/nova-api/${this.field.resource}`, {
                params: {
                    perPage: 100
                }
            })

            while (response) {
                let values = response.data.resources.map((r) => {
                    let label = _.find(r.fields, f => f.attribute === this.resourceLabel)
                    let value = _.find(r.fields, f => f.attribute === this.resourceValue)

                    return {
                        label: label && label.value || null,
                        value: value && value.value || null,
                    }
                })

                options = options.concat(values)

                if (response.data.next_page_url) {
                    response = await Nova.request().get(response.data.next_page_url, {
                        params: {
                            perPage: response.data.per_page
                        }
                    })
                } else {
                    response = null
                }
            }

            this.values = options
            this.isLoading = false
        }
    },
}
</script>

<style>
    .multiselect__spinner:before,
    .multiselect__spinner:after {
        border-color: var(--primary) transparent transparent;
    }

    .multiselect,
    .multiselect__input,
    .multiselect__single {
        font-size: inherit;
    }

    .multiselect {
        min-height: 2.25rem;
        color: var(--80);
    }

    .multiselect--disabled {
        background: #ededed;
        pointer-events: none;
        opacity: 0.6;
    }

    .multiselect__input,
    .multiselect__single {
        position: relative;
        display: inline-block;
        min-height: 20px;
        line-height: 20px;
        border: none;
        border-radius: .5rem;
        padding-top: .5rem !important;
    }

    .multiselect__input::placeholder {
        color: var(--80);
    }

    .multiselect__input:hover,
    .multiselect__single:hover {
        border-color: #cfcfcf;
    }

    .multiselect__single {
        padding-left: 5px;
        padding-top: .5rem;
    }

    .multiselect__tags-wrap {
        display: inline;
    }

    .multiselect__tags {
        min-height: 2.25rem;
        display: block;
        padding: 0 2rem 0 .75rem;
        border-radius: .5rem;
        border: 1px solid var(--60);
        background: #fff;
        font-size: initial;
    }

    .multiselect.border-danger > .multiselect__tags {
        border: 1px solid var(--danger);
    }

    .multiselect__tag {
        position: relative;
        display: inline-block;
        padding: 4px 26px 4px 10px;
        border-radius: 5px;
        margin-right: 10px;
        color: #fff;
        line-height: 1;
        background: #41b883;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        max-width: 100%;
        text-overflow: ellipsis;
    }

    .multiselect__tag-icon {
        cursor: pointer;
        margin-left: 7px;
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        font-weight: 700;
        font-style: initial;
        width: 22px;
        text-align: center;
        line-height: 22px;
        transition: all 0.2s ease;
        border-radius: 5px;
    }

    .multiselect__tag-icon:after {
        content: "×";
        color: #266d4d;
        font-size: initial;
    }

    .multiselect__tag-icon:focus,
    .multiselect__tag-icon:hover {
        background: #369a6e;
    }

    .multiselect__tag-icon:focus:after,
    .multiselect__tag-icon:hover:after {
        color: white;
    }

    .multiselect__current {
        line-height: 16px;
        min-height: 40px;
        box-sizing: border-box;
        display: block;
        overflow: hidden;
        padding: 8px 12px 0;
        padding-right: 30px;
        white-space: nowrap;
        margin: 0;
        text-decoration: none;
        border-radius: 5px;
        border: 1px solid #e8e8e8;
        cursor: pointer;
    }

    .multiselect__select {
        line-height: 16px;
        display: block;
        position: absolute;
        box-sizing: border-box;
        width: 40px;
        height: 38px;
        right: 1px;
        top: 1px;
        padding: 4px 8px;
        margin: 0;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .multiselect__select:before {
        position: relative;
        right: 0;
        top: 65%;
        color: #999;
        margin-top: 4px;
        border-style: solid;
        border-width: 5px 5px 0 5px;
        border-color: #999999 transparent transparent transparent;
        content: "";
    }

    .multiselect__placeholder {
        color: var(--80);
        display: inline-block;
        padding-top: .5rem;
    }

    .multiselect--active .multiselect__placeholder {
        display: none;
    }

    .multiselect__content-wrapper {
        position: absolute;
        display: block;
        background: #fff;
        width: 100%;
        max-height: 240px;
        overflow: auto;
        border: 1px solid #e8e8e8;
        border-top: none;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        z-index: 50;
        -webkit-overflow-scrolling: touch;
    }

    .multiselect__content {
        list-style: none;
        display: inline-block;
        padding: 0;
        margin: 0;
        min-width: 100%;
        vertical-align: top;
    }

    .multiselect--above .multiselect__content-wrapper {
        bottom: 100%;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        border-bottom: none;
        border-top: 1px solid #e8e8e8;
    }

    .multiselect__content::webkit-scrollbar {
        display: none;
    }

    .multiselect__element {
        display: block;
    }

    .multiselect__option {
        display: block;
        padding: 12px;
        min-height: 40px;
        line-height: 16px;
        text-decoration: none;
        text-transform: none;
        vertical-align: middle;
        position: relative;
        cursor: pointer;
        white-space: nowrap;
    }

    .multiselect__option:after {
        top: 0;
        right: 0;
        position: absolute;
        line-height: 40px;
        padding-right: 12px;
        padding-left: 20px;
        font-size: 13px;
    }

    .multiselect__option--highlight {
        background: #41b883;
        outline: none;
        color: white;
    }

    .multiselect__option--highlight:after {
        content: attr(data-select);
        background: #41b883;
        color: white;
    }

    .multiselect__option--selected {
        background: #f3f3f3;
        color: #35495e;
        font-weight: bold;
    }

    .multiselect__option--selected:after {
        content: attr(data-selected);
        color: silver;
    }

    .multiselect__option--selected.multiselect__option--highlight {
        background: #ff6a6a;
        color: #fff;
    }

    .multiselect__option--selected.multiselect__option--highlight:after {
        background: #ff6a6a;
        content: attr(data-deselect);
        color: #fff;
    }

    .multiselect--disabled .multiselect__current,
    .multiselect--disabled .multiselect__select {
        background: #ededed;
        color: #a6a6a6;
    }

    .multiselect__option--disabled {
        background: #ededed !important;
        color: #a6a6a6 !important;
        cursor: text;
        pointer-events: none;
    }

    .multiselect__option--group {
        background: #ededed;
        color: #35495e;
    }

    .multiselect__option--group.multiselect__option--highlight {
        background: #35495e;
        color: #fff;
    }

    .multiselect__option--group.multiselect__option--highlight:after {
        background: #35495e;
    }

    .multiselect__option--disabled.multiselect__option--highlight {
        background: #dedede;
    }

    .multiselect__option--group-selected.multiselect__option--highlight {
        background: #ff6a6a;
        color: #fff;
    }

    .multiselect__option--group-selected.multiselect__option--highlight:after {
        background: #ff6a6a;
        content: attr(data-deselect);
        color: #fff;
    }

    .multiselect-enter-active,
    .multiselect-leave-active {
        transition: all 0.15s ease;
    }

    .multiselect-enter,
    .multiselect-leave-active {
        opacity: 0;
    }

    .multiselect__strong {
        margin-bottom: 8px;
        line-height: 20px;
        display: inline-block;
        vertical-align: top;
    }

    *[dir="rtl"] .multiselect {
        text-align: right;
    }

    *[dir="rtl"] .multiselect__select {
        right: auto;
        left: 1px;
    }

    *[dir="rtl"] .multiselect__tags {
        padding: 8px 8px 0px 40px;
    }

    *[dir="rtl"] .multiselect__content {
        text-align: right;
    }

    *[dir="rtl"] .multiselect__option:after {
        right: auto;
        left: 0;
    }

    *[dir="rtl"] .multiselect__clear {
        right: auto;
        left: 12px;
    }

    *[dir="rtl"] .multiselect__spinner {
        right: auto;
        left: 1px;
    }

    @keyframes spinning {
        from {
            transform: rotate(0);
        }
        to {
            transform: rotate(2turn);
        }
    }
</style>
