<template>
    <div class="card p-3">
        <h3 class="subtitle">
            Videos
            <a href="/videos/new" class="button is-primary is-pulled-right">Upload Video</a>
        </h3>

        <div v-for="videos in chunkedVideos" class="columns">
            <div v-for="video in videos" class="column">
                <a :href="video.url">{{ video.name }}</a>
            </div>
        </div>
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

        computed: {
            chunkedVideos() {
                return _.chunk(this.videos, 4);
            }
        },

        methods: {
            fetchVideos() {
                axios.get(`/api/videos/list/${this.user.name}`)
                    .then(r => this.videos = r.data);
            }
        },

        created() {
            this.fetchVideos();
        }
    }
</script>
