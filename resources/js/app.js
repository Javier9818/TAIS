/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');


import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)


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

Vue.component('table-entidades', require('./components/empresa/TableEntidades.vue').default);
Vue.component('form-entidad', require('./components/empresa/FormEntidad.vue').default);

Vue.component('table-unidades-negocio', require('./components/empresa/TableUnidadesNegocio.vue').default);
Vue.component('form-unidad-negocio', require('./components/empresa/FormUnidadNegocio.vue').default);

Vue.component('componente-administrar-cadena', require('./components/empresa/cadena_suministro/Administrar.vue').default);

Vue.component('componente-cadena-cliente', require('./components/empresa/cadena_suministro/Clientes.vue').default);
Vue.component('form-cadena-cliente', require('./components/empresa/cadena_suministro/FormCliente.vue').default);

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
