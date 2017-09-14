<template>
    <div>
        <video
            loop
            controls
            autoplay
            preload="auto"
            width="100%"
            height="540"
            :class="videoClasses"
            :poster="media.thumbnail"
            :data-setup="videoSetupJson">
            <source :src="media.stream_url" type="video/mp4" v-if="media.stream_url && media.unlocked">
        </video>

        <div class="card p-3 m-t-3">
            <h2 class="video-title">
                {{ media.name }}

                <f-subscribe
                    v-if="! media.is_mine && media.required_subscription"
                    class="is-pulled-right"
                    :tag="subscribeTag"
                    :user="media.user"
                    :plan="media.required_subscription"
                    @success="subscribeSuccess">
                </f-subscribe>

                <f-purchase
                    v-if="! media.is_mine && media.token_price"
                    class="is-pulled-right"
                    type="video"
                    :amount="media.token_price"
                    :id="media.id"
                    @success="purchaseSuccess">
                </f-purchase>
            </h2>

            <span class="video-meta">
                <a :href="media.user.profile_url">{{ media.user.name }}</a>
                &middot;
                {{ media.created_at | fromnow }}
            </span>

            <span class="video-meta">
                <f-rating type="video" :id="media.id" class="is-pulled-left"></f-rating>
            </span>
        </div>

        <div class="card p-3 m-t-3" v-if="media.is_mine">
            <a :href="media.edit_url" class="button is-primary">
                <i class="material-icons m-r-2">settings</i>
                Manage Video
            </a>
        </div>

        <div class="card p-3 m-t-3">
            Comments
        </div>
    </div>
</template>

<script>
    export default {
        props: ['video'],

        data() {
            return {
                media: this.video,
                videoSetup: {
                    fluid: true
                }
            };
        },

        computed: {
            videoClasses() {
                return {
                    'video-js': true,
                    'vjs-default-skin': true,
                    'vjs-big-play-centered': true,
                    'vjs-16-9': true
                };
            },

            videoSetupJson() {
                return JSON.stringify(this.videoSetup);
            },

            subscribeTag() {
                return `You need to be a subscriber (${this.video.required_subscription} or higher)`;
            }
        },

        methods: {
            purchaseSuccess() {
                this.$toast.open({
                    message: 'Video Purchased',
                    type: 'is-success',
                    duration: 4000
                });

                this.fetch();
            },

            subscribeSuccess() {
                this.fetch();
            },

            fetch() {
                return ajax.get(`/api/videos/${this.media.id}`)
                    .then(r => this.media = r.data);
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
