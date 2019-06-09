<template>
    <panel-item :field="field">
        <template slot="value">
            <router-link
                v-if="field.resource && field.value"
                :to="{
                    name: 'detail',
                    params: {
                        resourceName: field.resource,
                        resourceId: field.value,
                    },
                }"
                class="no-underline font-bold dim text-primary"
            >
                {{ field.resourceValue }}
            </router-link>
            <p v-else-if="field.value">{{ field.value }}</p>
            <p v-else>&mdash;</p>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
    }
</script>
