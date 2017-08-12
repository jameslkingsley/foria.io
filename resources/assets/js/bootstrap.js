window._ = require('lodash');

window.positions = require('positions');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.Vue = require('vue');

import Buefy from 'buefy';
Vue.use(Buefy);

Vue.filter('currency', (value) => {
    let langage = (navigator.language || navigator.browserLanguage).split('-')[0];

    return (value / 100).toLocaleString(langage, {
        style: 'currency',
        currency: 'usd'
    });
});

import Echo from 'laravel-echo'
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '87ebcedc0427d5204a31', // TODO
    cluster: 'eu'
});
