require('./bootstrap');
require('./stream');

require('webrtc-adapter');
import Broadcaster from './broadcaster/index';
window.Broadcaster = Broadcaster;

// Profile
Vue.component('f-profile', require('./components/profile/Index.vue'));

// Videos
Vue.component('f-video', require('./components/video/Index.vue'));
Vue.component('f-video-upload', require('./components/video/Upload.vue'));
Vue.component('f-video-edit', require('./components/video/Edit.vue'));
Vue.component('f-video-list', require('./components/video/List.vue'));
Vue.component('f-video-comments', require('./components/video/Comments.vue'));

// Ratings
Vue.component('f-rating', require('./components/Rating.vue'));

// Reporting
Vue.component('f-report', require('./components/Report.vue'));

// Purchases & Subscriptions
Vue.component('f-purchase', require('./components/Purchase.vue'));
Vue.component('f-subscribe', require('./components/Subscribe.vue'));

// Watch
Vue.component('f-watch', require('./components/watch/Index.vue'));
Vue.component('f-watch-follow', require('./components/watch/Follow.vue'));
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
Vue.component('f-settings-stats', require('./components/settings/Stats.vue'));

// Form
Vue.component('f-form', require('./components/form/Form.vue'));
Vue.component('f-form-button', require('./components/form/Button.vue'));
Vue.component('f-form-image-upload', require('./components/form/ImageUpload.vue'));
Vue.component('f-form-video-upload', require('./components/form/VideoUpload.vue'));
Vue.component('f-modal-form', require('./components/form/ModalForm.vue'));

// Modal
Vue.component('f-modal', require('./components/Modal.vue'));

// Stream Testing
Vue.component('f-stream-test', require('./components/StreamTest.vue'));

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
            })
            .listen('TranscodeCompleted', e => {
                this.$toast.open({
                    message: 'Video Processing Complete',
                    type: 'is-success',
                    duration: 4000
                });
            });
    }
});
