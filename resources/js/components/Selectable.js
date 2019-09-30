import _ from 'lodash'
import storage from './../VueSelectFieldStorage'

export default {
    data: () => ({
        isLoading: false,
        isMultiple: false,
        availableResources: [],
        initializingWithExistingResources: false,
        selectedResources: null,
        selectedResourcesIds: null,
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
                    if (this.initializingWithExistingResources) {
                        this.withTrashed = withTrashed
                    }

                    // Turn off initializing the existing resource after the first time
                    this.initializingWithExistingResources = false
                    this.availableResources = resources
                    this.softDeletes = softDeletes
                    this.isLoading = false
                })
        },

        /**
         * Determine if the related resource is soft deleting.
         */
        determineIfSoftDeletes() {
            return storage.determineIfSoftDeletes(this.searchableResourceName).then(response => {
                this.softDeletes = response.data.softDeletes
            })
        },

        /**
         * Select the initial selected resources
         */
        selectInitialResources() {
            this.selectedResources = _.filter(this.availableResources, r => this.selectedResourcesIds.includes(r.value))
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
                this.selectedResources = []
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
                    current: this.selectedResourcesIds,
                    first: this.initializingWithExistingResource,
                    search: this.search,
                    withTrashed: this.withTrashed,
                    multiple: this.isMultiple,
                },
            }
        },
    }
}
