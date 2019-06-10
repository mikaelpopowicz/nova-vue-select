<template>
    <panel-item :field="field">
        <template slot="value">
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
            <p v-else-if="field.resourceName">&mdash;</p>
            <p v-else class="text-danger">{{ __('Resource is not defined') }}</p>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resourceName', 'resourceId', 'field'],
    }
</script>
