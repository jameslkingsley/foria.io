<template>
    <div class="card p-3">
        <form enctype="multipart/form-data">
            <div class="columns m-0" v-show="filePicked">
                <div class="column is-4 is-offset-4">
                    <div class="w100 has-text-centered p-5">
                        <progress class="progress is-primary" :value="progress" :max="total">{{ progress }}%</progress>
                    </div>
                </div>
            </div>

            <f-form-video-upload
                name="video"
                v-show="! filePicked"
                :uploaded.sync="uploaded"
                @loaded="uploadVideo">
            </f-form-video-upload>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                uploaded: false,
                filePicked: false,
                progress: 0,
                total: 0
            };
        },

        methods: {
            uploadVideo(video) {
                this.filePicked = true;

                axios.post(`/api/videos`, video.form)
                    .then(r => {
                        this.uploaded = true;
                        // window.location.href = r.data.edit_url;
                    });
            }
        },

        created() {
            Echo.private(`App.User.${Foria.user.id}`)
                .listen('UploadProgress', e => {
                    this.total = e.total;
                    this.progress = e.progress;
                });
        }
    }
</script>
