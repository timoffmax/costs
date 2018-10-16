
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

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

// Vue progress bar
import VueProgressBar from 'vue-progressbar';
const options = {
    color: 'orange',
    failedColor: '#874b4b',
    thickness: '5px',
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
