require('./bootstrap');
require('./stream');

Vue.component('f-watch', require('./components/Watch.vue'));
Vue.component('f-follow', require('./components/Follow.vue'));
Vue.component('f-subscribe', require('./components/Subscribe.vue'));

const app = new Vue({
    el: '#app'
});
