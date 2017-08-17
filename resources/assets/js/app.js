require('./bootstrap');
require('./stream');

Vue.component('f-watch', require('./components/Watch.vue'));
Vue.component('f-follow', require('./components/Follow.vue'));
Vue.component('f-subscribe', require('./components/Subscribe.vue'));
Vue.component('f-token-checkout', require('./components/TokenCheckout.vue'));
Vue.component('f-broadcast-list', require('./components/BroadcastList.vue'));
Vue.component('f-broadcast-tile', require('./components/BroadcastTile.vue'));
Vue.component('f-chat', require('./components/Chat.vue'));

// Settings
Vue.component('f-settings', require('./components/settings/Index.vue'));
Vue.component('f-settings-account', require('./components/settings/Account.vue'));
Vue.component('f-settings-billing', require('./components/settings/Billing.vue'));
Vue.component('f-settings-subscriptions', require('./components/settings/Subscriptions.vue'));
Vue.component('f-settings-notifications', require('./components/settings/Notifications.vue'));

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
