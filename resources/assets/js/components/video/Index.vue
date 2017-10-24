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
            <source :src="media.stream_url" type="video/mp4" v-if="media.stream_url">
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
                    :amount="media.token_price"
                    :reference="media.ref"
                    @success="purchaseSuccess">
                </f-purchase>
            </h2>

            <span class="video-meta">
                {{ media.views | locale }} views
                &middot;
                <a class="font-bold" :href="media.user.profile_url">{{ media.user.name }}</a>
                &middot;
                {{ media.created_at | fromnow }}
            </span>

            <span class="video-meta">
                <f-rating :reference="media.ref" class="is-pulled-left"></f-rating>

                <f-report :reference="media.ref" class="is-pulled-right"></f-report>
            </span>
        </div>

        <div class="columns">
            <div class="column is-9">
                <div class="card p-3 m-t-3">
                    <f-video-comments :preload="video.comments" :reference="media.ref"></f-video-comments>
                </div>
            </div>

            <div class="column is-3">
                <div class="card p-3 m-t-3">
                    <h1 class="subtitle">Recommended</h1>
                </div>
            </div>
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
                return ajax.get(`/api/videos/${this.media.ref}`)
                    .then(r => this.media = r.data);
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
