require('./bootstrap');
require('./stream');

Vue.component('f-watch', require('./components/Watch.vue'));

const app = new Vue({
    el: '#app'
});
