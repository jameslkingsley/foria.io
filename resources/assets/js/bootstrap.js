window._ = require('lodash');

require('./utils');

window.moment = require('moment');

window.formToObject = require('form_to_object');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

axios.interceptors.response.use(response => {
    Event.fire('f-form-submitting', false);
    return response;
}, error => {
    return Promise.reject(error);
});

window.Vue = require('vue');

import Buefy from 'buefy';
Vue.use(Buefy);

require('./filters');
require('./event');

import Echo from 'laravel-echo'
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: Foria.pusherKey,
    cluster: 'eu'
});
