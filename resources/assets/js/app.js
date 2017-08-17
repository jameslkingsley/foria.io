require('./bootstrap');
require('./stream');

Vue.component('f-watch', require('./components/Watch.vue'));
Vue.component('f-follow', require('./components/Follow.vue'));
Vue.component('f-subscribe', require('./components/Subscribe.vue'));
Vue.component('f-token-checkout', require('./components/TokenCheckout.vue'));
Vue.component('f-broadcast-list', require('./components/BroadcastList.vue'));
Vue.component('f-broadcast-tile', require('./components/BroadcastTile.vue'));
Vue.component('f-chat', require('./components/Chat.vue'));

const app = new Vue({
    el: '#app',

    data: {
        user: {
            tokens: Foria.user ? Foria.user.tokens : 0
        }
    },

    created() {
        if (! Foria.user) return;

        Echo.private(`App.User.${Foria.user.id}`)
            .listen('TokensAdded', e => {
                this.user.tokens = e.total;
            });
    }
});
