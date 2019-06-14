Nova.booting((Vue, router, store) => {
    Vue.component('index-vue-select', require('./components/IndexField'))
    Vue.component('detail-vue-select', require('./components/DetailField'))
    Vue.component('form-vue-select', require('./components/FormField'))
    Vue.component('vue-select-filter', require('./components/Filter'))
})
