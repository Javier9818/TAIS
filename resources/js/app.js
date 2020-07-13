/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('table-empresa', require('./components/admin/TableEmpresa.vue').default);
Vue.component('form-empresa', require('./components/admin/FormEmpresa.vue').default);
Vue.component('table-users', require('./components/admin/TableUsers.vue').default);
Vue.component('form-user', require('./components/admin/FormUser.vue').default);

Vue.component('table-clientes', require('./components/empresa/TableClientes.vue').default);
Vue.component('table-proveedores', require('./components/empresa/TableProveedores.vue').default);
Vue.component('form-cliente', require('./components/empresa/FormCliente.vue').default);
Vue.component('form-proveedor', require('./components/empresa/FormProveedor.vue').default);



import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
