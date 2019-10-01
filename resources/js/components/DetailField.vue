<template>
    <panel-item :field="field">
        <template slot="value">
            <ul v-if="field.resourceName && field.selectedResourcesDisplay.length">
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
            <p v-else-if="field.resourceName">&mdash;</p>
            <p v-else class="text-danger">{{ __('Resource is not defined') }}</p>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resourceName', 'field'],
    }
</script>
