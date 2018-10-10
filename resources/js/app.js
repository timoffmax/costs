
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Libs
import moment from 'moment';

// Vue Router init
import VueRouter from 'vue-router';
Vue.use(VueRouter);

// Form validation
import { Form, HasError, AlertError } from 'vform';

window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

// List of routes
import { routes } from './routes';
const router = new VueRouter({mode: 'history', routes: routes});

// Global filters
import Vue2Filters from 'vue2-filters';
Vue.use(Vue2Filters);

Vue.filter('dateMoment', function (date, format) {
    if (!date) {
        return '';
    }

    return moment(date).format(format);
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router
});
