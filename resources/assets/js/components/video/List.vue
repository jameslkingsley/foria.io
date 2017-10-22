<template>
    <div class="video-list">
        <f-video-item
            v-for="(video, index) in videos"
            :key="index"
            :video="video">
        </f-video-item>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                videos: []
            };
        },

        methods: {
            fetchVideos() {
                return axios.get(`/api/videos/list/${this.user.name}`)
                    .then(r => this.videos = r.data);
            }
        },

        created() {
            this.fetchVideos();
        }
    }
</script>
