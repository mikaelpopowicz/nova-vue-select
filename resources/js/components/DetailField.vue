<template>
    <panel-item :field="field">
        <template slot="value">
            <template v-if="field.resourceName && field.selectedResourcesDisplay.length">
                <ul v-if="isMultiple">
                    <li v-for="resource in field.selectedResourcesDisplay">
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
                    </li>
                </ul>
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
            </template>
            <p v-else-if="field.resourceName">&mdash;</p>
            <p v-else class="text-danger">{{ __('Resource is not defined') }}</p>
        </template>
    </panel-item>
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
