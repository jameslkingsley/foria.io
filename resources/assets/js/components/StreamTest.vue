<template>
    <div class="card">
        <button class="button m-r-2" @click="presenter">
            Presenter
        </button>

        <button class="button m-r-2" @click="viewer">
            Viewer
        </button>

        <button class="button" @click="terminate">
            Stop
        </button>

        <video ref="video" controls autoplay width="640px" height="480px"></video>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                //
            };
        },

        methods: {
            presenter() {
                LiveStream.presenter();
            },

            viewer() {
                LiveStream.viewer();
            },

            terminate() {
                LiveStream.stop();
            }
        },

        mounted() {
            LiveStream.setVideo(this.$refs.video);
        }
    }
</script>
