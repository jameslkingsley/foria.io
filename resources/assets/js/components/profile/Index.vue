<template>
    <div class="card p-3">
        <div :class="uploadModalClasses">
            <div class="modal-background" @click="showUploadModal = false"></div>

            <div class="modal-content card p-4">
                <f-form confirm="Start Upload">
                    <f-form-video-upload name="video" @loaded="uploadVideo"></f-form-video-upload>
                </f-form>
            </div>

            <button class="modal-close is-large" aria-label="close" @click="showUploadModal = false"></button>
        </div>

        <h3 class="subtitle">
            Videos
            <button class="button is-primary is-pulled-right" @click="showUploadModal = true">Upload Video</button>
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
                videos: [],
                showUploadModal: false
            };
        },

        computed: {
            chunkedVideos() {
                return _.chunk(this.videos, 4);
            },

            uploadModalClasses() {
                return {
                    'modal': true,
                    'is-active': this.showUploadModal
                };
            }
        },

        methods: {
            fetchVideos() {
                axios.get(`/api/videos/list/${this.user.name}`)
                    .then(r => this.videos = r.data);
            },

            uploadVideo(video) {
                console.log(video.form);

                axios.post(`/api/videos`, video.form, {
                    onUploadProgress(e) {
                        // let percent = Math.floor((e.loaded * 100) / e.total);
                        console.log(e);
                    }
                });
            }
        },

        created() {
            this.fetchVideos();
        }
    }
</script>
