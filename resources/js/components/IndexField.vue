<template>
    <span>
        <span v-if="field.resource && field.value">
            <router-link
                :to="{
                    name: 'detail',
                    params: {
                        resourceName: field.resource,
                        resourceId: field.value,
                    },
                }"
                    class="no-underline dim text-primary font-bold"
            >
                {{ field.resourceValue }}
            </router-link>
        </span>
        <span v-else-if="field.value">{{ field.value }}</span>
        <span v-else>&mdash;</span>
    </span>
</template>

<script>
    export default {
        props: ['resourceName', 'field'],
    }
</script>
