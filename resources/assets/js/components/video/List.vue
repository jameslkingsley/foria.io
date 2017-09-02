<template>
    <div>
        <div v-for="videos in chunkedVideos" class="video-list">
            <div v-for="video in videos" class="video-item">
                <a :href="video.url" class="video">
                    <div class="video-thumbnail" :style="getStyle(video)">
                        <span class="video-duration">
                            {{ video.duration | duration }}
                        </span>

                        <span class="video-hd">
                            {{ (video.height >= 720) ? 'HD' : '' }}
                            {{ video.height }}P
                        </span>
                    </div>

                    <span class="video-name" v-text="video.name"></span>

                    <span class="video-meta">
                        {{ video.created_at | fromnow }}
                        &middot;
                        {{ user.name }}
                    </span>
                </a>
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
                return axios.get(`/api/videos/list/${this.user.name}`)
                    .then(r => this.videos = r.data);
            },

            getStyle(video) {
                return {
                    'background-image': `url(${video.thumbnail})`
                };
            }
        },

        created() {
            this.fetchVideos();
        }
    }
</script>
