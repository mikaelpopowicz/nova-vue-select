<template>
    <span>
        <span v-if="field.resourceName && field.selectedResourcesDisplay.length">
            <template v-if="isMultiple">
                <template v-for="(resource, index) in field.selectedResourcesDisplay">
                    <router-link

                        :to="{
                            name: 'detail',
                            params: {
                                resourceName: field.resourceName,
                                resourceId: resource.id,
                            },
                        }"
                        class="no-underline font-bold dim text-primary"
                    >
                        {{ resource.display }}
                    </router-link>
                    <span v-if="index < field.selectedResourcesDisplay.length - 1"> | </span>
                </template>
            </template>
            <router-link
                v-else
                :to="{
                    name: 'detail',
                    params: {
                        resourceName: field.resourceName,
                        resourceId: field.selectedResourcesDisplay[0].id,
                    },
                }"
                class="no-underline font-bold dim text-primary"
            >
                {{ field.selectedResourcesDisplay[0].display }}
            </router-link>
        </span>
        <span v-else-if="field.resourceName">&mdash;</span>
        <span v-else class="text-danger">{{ __('Resource is not defined') }}</span>
    </span>
</template>

<script>
    import Selectable from './Selectable'

    export default {
        props: ['resourceName', 'field'],
        mixins: [Selectable],

        mounted() {
            this.isMultiple = this.field.multiple
        }
    }
</script>
