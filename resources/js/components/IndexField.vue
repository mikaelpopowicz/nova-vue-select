<template>
    <span>
        <span v-if="field.resource && field.value">
            <router-link
                    v-if="field.resourceName && field.value"
                    :to="{
                    name: 'detail',
                    params: {
                        resourceName: field.resourceName,
                        resourceId: field.value,
                    },
                }"
                    class="no-underline font-bold dim text-primary"
            >
                {{ field.selectedResourceDisplay }}
            </router-link>
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
