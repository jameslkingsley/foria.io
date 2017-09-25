window._ = require('lodash');

require('./utils');
window.videojs = require('video.js/dist/video');

require('adapterjs');
window.kurentoUtils = require('kurento-utils');

window.moment = require('moment');
require('moment-duration-format');

window.formToObject = require('form_to_object');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = Foria.csrfToken;

window.axios.interceptors.response.use(response => {
    ForiaEvent.fire('f-form-submitting', false);
    return response;
}, error => {
    ForiaEvent.fire('f-form-submitting', false);
    return Promise.reject(error);
});

window.ajax = window.axios;

window.Vue = require('vue');

let VueResource = require('vue-resource');
Vue.use(VueResource);

Vue.http.headers.common['X-CSRF-TOKEN'] = Foria.csrfToken;

import Buefy from 'buefy';
Vue.use(Buefy);

require('./filters');
require('./event');

import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: Foria.pusherKey,
    cluster: 'eu'
});
