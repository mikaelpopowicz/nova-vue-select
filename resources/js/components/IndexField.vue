<template>
    <span>
        <span v-if="field.resourceName && field.selectedResourcesDisplay.length">
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
        </span>
        <span v-else-if="field.resourceName">&mdash;</span>
        <span v-else class="text-danger">{{ __('Resource is not defined') }}</span>
    </span>
</template>

<script>
    export default {
        props: ['resourceName', 'field'],
    }
</script>
