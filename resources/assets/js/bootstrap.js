window._ = require('lodash');

require('./utils');

window.moment = require('moment');

window.formToObject = require('form_to_object');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = Foria.csrfToken;

axios.interceptors.response.use(response => {
    Event.fire('f-form-submitting', false);
    return response;
}, error => {
    Event.fire('f-form-submitting', false);
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
