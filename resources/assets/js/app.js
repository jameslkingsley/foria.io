require('./bootstrap');
require('./stream');

// Watch
Vue.component('f-watch', require('./components/watch/Index.vue'));
Vue.component('f-watch-follow', require('./components/watch/Follow.vue'));
Vue.component('f-watch-subscribe', require('./components/watch/Subscribe.vue'));
Vue.component('f-watch-chat', require('./components/watch/Chat.vue'));

// Tokens
Vue.component('f-token-checkout', require('./components/TokenCheckout.vue'));

// Broadcasts
Vue.component('f-broadcast-list', require('./components/BroadcastList.vue'));
Vue.component('f-broadcast-tile', require('./components/BroadcastTile.vue'));

// Settings
Vue.component('f-settings', require('./components/settings/Index.vue'));
Vue.component('f-settings-account', require('./components/settings/Account.vue'));
Vue.component('f-settings-billing', require('./components/settings/Billing.vue'));
Vue.component('f-settings-subscriptions', require('./components/settings/Subscriptions.vue'));
Vue.component('f-settings-notifications', require('./components/settings/Notifications.vue'));
Vue.component('f-settings-model', require('./components/settings/Model.vue'));

// Form
Vue.component('f-form', require('./components/form/Form.vue'));
Vue.component('f-form-button', require('./components/form/Button.vue'));
Vue.component('f-modal-form', require('./components/form/ModalForm.vue'));

// Modal
Vue.component('f-modal', require('./components/Modal.vue'));

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
