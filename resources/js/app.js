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
import auth from './components/auth/auth.js';

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
const mainComponent = Vue.component('mainComponent', require('./components/mainComponent.vue').default);
const dashboard = Vue.component('dashboard', require('./components/dashboard.vue').default);
const tarifario = Vue.component('tarifario', require('./components/tarifario.vue').default);
const alertas = Vue.component('alertas', require('./components/alertas.vue').default);
const relatorios = Vue.component('relatorios', require('./components/relatorios.vue').default);
const listaRelatorios = Vue.component('listaRelatorios', require('./components/relatorios/listaRelatorios.vue').default);

/*
const echartString =  Vue.component('echartString', require('./components/charts/echartString.vue').default);
const echart =  Vue.component('echart', require('./components/charts/echart.vue').default);
const chartjs =  Vue.component('chartjs', require('./components/charts/chart.js.vue').default);
const googlechart = Vue.component('googlechart', require('./components/charts/googlechart.vue').default);
const googlechartMaterial = Vue.component('googlechartMaterial', require('./components/charts/googlechartMaterial.vue').default);
const plotly = Vue.component('plotly', require('./components/charts/plotly.vue').default);
*/

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const routes = [
    /*
    { path: '/', redirect: '/login' },
    { path: '/#', redirect: '/login' },
    */
    { path: '/login', component: login },
    { path: '/register', component: register },
    { path: '/example', component: example },
    { path: '/', component: mainComponent,
        children: [
            { path: '/dashboard', component: dashboard},
            { path: '/tarifario', component: tarifario},
            { path: '/alertas', component: alertas},
            { path: '/relatorios', component: relatorios,
        children: [
            { path: '/relatorios/:year', component: listaRelatorios,}]},
    ]},
    /*
    { path: '/dashboard', component: dashboard,
        children: [
            { path: 'echartString', component: echartString },
            { path: 'echart', component: echart },
            { path: 'plotly', component: plotly },
            { path: 'chartjs', component: chartjs },
            { path: 'googlechart', component: googlechart },
            { path: 'googlechartMaterial', component: googlechartMaterial },
        ]
    },*/
];



const router = new VueRouter({
    routes:routes
});

router.beforeEach((to, from, next) => {
    if ( to.path !== '/login' && to.path !== '/register'){
        if (!auth.user) next('/login')
        else next()
    }
    else next()
})

axios.defaults.headers.common['X-CSRF-TOKEN'] = Laravel.csrfToken;

const app = new Vue({
    el: '#app',
    data:{
        show: true,
    },
    router,
});
