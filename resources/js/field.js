Nova.booting((Vue, router) => {
    Vue.component('index-nova-vat-validation', require('./components/IndexField'));
    Vue.component('detail-nova-vat-validation', require('./components/DetailField'));
    Vue.component('form-nova-vat-validation', require('./components/FormField'));
})
