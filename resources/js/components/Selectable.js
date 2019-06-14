import _ from 'lodash'
import storage from './../VueSelectFieldStorage'

export default {
    data: () => ({
        isLoading: false,
        availableResources: [],
        initializingWithExistingResource: false,
        selectedResource: null,
        selectedResourceId: null,
        softDeletes: false,
        withTrashed: false,
        search: '',
    }),

    methods: {
        /**
         * Get the resources that may be related to this resource.
         */
        getAvailableResources() {
            this.isLoading = true
            return storage
                .fetchAvailableResources(this.searchableResourceName, this.queryParams)
                .then(({ data: { resources, softDeletes, withTrashed } }) => {
                    if (this.initializingWithExistingResource) {
                        this.withTrashed = withTrashed
                    }

                    // Turn off initializing the existing resource after the first time
                    this.initializingWithExistingResource = false
                    this.availableResources = resources
                    this.softDeletes = softDeletes
                    this.isLoading = false
                })
        },

        /**
         * Determine if the relatd resource is soft deleting.
         */
        determineIfSoftDeletes() {
            return storage.determineIfSoftDeletes(this.searchableResourceName).then(response => {
                this.softDeletes = response.data.softDeletes
            })
        },

        /**
         * Select the initial selected resource
         */
        selectInitialResource() {
            this.selectedResource = _.find(
                this.availableResources,
                r => r.value == this.selectedResourceId
            )
        },

        /**
         * Perform a search to get the relatable resources.
         */
        performSearch(search) {
            this.isLoading = true

            const trimmedSearch = search.trim()
            // If the user performs an empty search, it will load all the results
            // so let's just set the availableResources to an empty array to avoid
            // loading a huge result set
            if (trimmedSearch === '') {
                this.clearSelection()

                return
            }
            this.search = trimmedSearch

            this.debouncer(() => {
                this.selectedResource = ''
                this.getAvailableResources()
            }, 500)
        },

        /**
         * Clear the selected resource and availableResources
         */
        clearSelection() {
            this.isLoading = false
        },
    },

    computed: {
        searchableResourceName() {
            return this.field.resourceName
        },

        /**
         * Get the query params for getting available resources
         */
        queryParams() {
            return {
                params: {
                    current: this.selectedResourceId,
                    first: this.initializingWithExistingResource,
                    search: this.search,
                    withTrashed: this.withTrashed,
                },
            }
        },
    }
}
