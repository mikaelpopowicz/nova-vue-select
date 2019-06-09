Nova.booting((Vue, router, store) => {
    Vue.component('index-nova-vue-select', require('./components/IndexField'))
    Vue.component('detail-nova-vue-select', require('./components/DetailField'))
    Vue.component('form-nova-vue-select', require('./components/FormField'))
})
