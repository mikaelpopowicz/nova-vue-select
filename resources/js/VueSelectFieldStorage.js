export default {
    fetchAvailableResources(resourceName, params) {
        return Nova.request().get(
            `/nova-vendor/vue-select/${resourceName}`,
            params
        )
    },

    determineIfSoftDeletes(resourceName) {
        return Nova.request().get(`/nova-api/${resourceName}/soft-deletes`)
    },
}
