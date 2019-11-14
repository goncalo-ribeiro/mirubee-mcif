/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('vue-events');

window.Vue = require('vue');
window.Event = new Vue();
//var Cookies = require('js-cookie');


import VueRouter from 'vue-router';
import Auth from './components/auth/auth.js';
import Toasted from 'vue-toasted';

Vue.use(VueRouter);
Vue.use(Auth);
Vue.use(Toasted, {
    iconPack : 'material', // set your iconPack, defaults to material. material|fontawesome|custom-class
    duration : 3000,
})

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

const example = Vue.component('example', require('./components/ExampleComponent.vue').default);
const login = Vue.component('login', require('./components/auth/login.vue').default);
const register = Vue.component('register', require('./components/auth/register.vue').default);
const dashboard = Vue.component('dashboard', require('./components/dashboard.vue').default);
const echart =  Vue.component('echart', require('./components/charts/echart.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/#', redirect: '/login' },
    { path: '/login', component: login },
    { path: '/register', component: register },
    { path: '/example', component: example },
    { path: '/dashboard', component: dashboard,
        children: [
            { path: 'echart', component: echart }        
        ]
    },
];

const router = new VueRouter({
    routes:routes
});

axios.defaults.headers.common['X-CSRF-TOKEN'] = Laravel.csrfToken;

const app = new Vue({
    el: '#app',
    data:{
        show: true,
    },
    router,
});
