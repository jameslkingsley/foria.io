require('./bootstrap');
require('./stream');

Vue.component('f-watch', require('./components/Watch.vue'));
Vue.component('f-follow', require('./components/Follow.vue'));
Vue.component('f-subscribe', require('./components/Subscribe.vue'));
Vue.component('f-token-checkout', require('./components/TokenCheckout.vue'));

const app = new Vue({
    el: '#app'
});
