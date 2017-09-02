<template>
    <div>
        <video
            :class="videoClasses"
            controls
            preload="auto"
            width="100%"
            height="540"
            :poster="video.thumbnail"
            :data-setup="videoSetupJson">
            <source :src="video.stream_url" type="video/mp4" v-if="video.stream_url">
        </video>

        <div class="card p-3 m-t-3">
            <h2 class="video-title">
                {{ video.name }}
                <a :href="video.edit_url" class="button is-primary is-pulled-right" v-if="video.is_mine">
                    <i class="material-icons m-r-2">settings</i>
                    Manage Video
                </a>
            </h2>

            <span class="video-meta">
                <a :href="video.user.profile_url">{{ video.user.name }}</a>
                &middot;
                {{ video.created_at | fromnow }}
            </span>
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
            }
        }
    }
</script>
