
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue';
window.Vue = Vue;

/**
 * Add libs
 */
import moment from 'moment';
import swal from 'sweetalert2';

window.moment = moment;
window.swal = swal;

/**
 * Add Vue plugins
 */
// Vue Router
import VueRouter from 'vue-router';
Vue.use(VueRouter);
import { routes } from './routes';
const router = new VueRouter({mode: 'history', routes: routes});

// Form validation
import { Form, HasError, AlertError } from 'vform';
window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

// Global filters
import Vue2Filters from 'vue2-filters';
Vue.use(Vue2Filters);
Vue.filter('dateMoment', function (date, format) {
    if (!date) {
        return '';
    }

    return moment(date).format(format);
});
Vue.filter('transactionAmount', function (transaction) {
    let formattedValue;

    switch (transaction.type.name) {
        case 'cost':
            formattedValue = `-${transaction.sum}`;
            break;

        case 'income':
        default:
            formattedValue = transaction.sum;
    }

    return formattedValue;
});
Vue.filter('price', function (price) {
    if (typeof price !== "number") {
        return price;
    }

    return price.toFixed(2);
});

// Vue progress bar
import VueProgressBar from 'vue-progressbar';
const options = {
    color: '#38c172',
    failedColor: '#874b4b',
    thickness: '4px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    location: 'top',
    inverse: false
};
Vue.use(VueProgressBar, options);

// Setup notifications
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
window.toast = toast;

// Add SVG support
Vue.prototype.svg = require('./svg');

// Pagination
import Pagination from 'laravel-vue-pagination';
Vue.component(
    'pagination',
    Pagination
);

// Good table plugin
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css'
Vue.use(VueGoodTablePlugin);

/**
 * Add Laravel Passport components
 */
import Clients from './components/passport/Clients.vue';
import AuthorizedClients from './components/passport/AuthorizedClients.vue';
import PersonalAccessTokens from './components/passport/PersonalAccessTokens.vue';

Vue.component(
    'passport-clients',
    Clients
);

Vue.component(
    'passport-authorized-clients',
    AuthorizedClients
);

Vue.component(
    'passport-personal-access-tokens',
    PersonalAccessTokens
);

/**
 * Add custom components
 */
import ForbiddenPage from './components/errors/Forbidden';

Vue.component(
    'forbidden-page',
    ForbiddenPage
);
Vue.component(
    'not-found-page',
    ForbiddenPage
);

/**
 * Import ACL policies
 */
import Gate from './Gate';
Vue.prototype.$gate = new Gate(window.user);

/**
 * Create Vue instance
 */

const app = new Vue({
    el: '#app',
    router,
    mounted () {
        //  [App.vue specific] When App.vue is finish loading finish the progress bar
        this.$Progress.finish()
    },
    created () {
        //  [App.vue specific] When App.vue is first loaded start the progress bar
        this.$Progress.start();

        //  hook the progress bar to start before we move router-view
        this.$router.beforeEach((to, from, next) => {
            //  does the page we want to go to have a meta.progress object
            if (to.meta.progress !== undefined) {
                let meta = to.meta.progress
                // parse meta tags
                this.$Progress.parseMeta(meta)
            }
            //  start the progress bar
            this.$Progress.start()
            //  continue to next page
            next()
        });

        //  hook the progress bar to finish after we've finished moving router-view
        this.$router.afterEach((to, from) => {
            //  finish the progress bar
            this.$Progress.finish()
        });
    }
});
