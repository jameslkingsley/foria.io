<template>
    <div class="card p-3">
        <form enctype="multipart/form-data" v-show="! processing">
            <div class="columns m-0" v-show="filePicked">
                <div class="column is-4 is-offset-4">
                    <div class="w100 has-text-centered p-5">
                        <progress class="progress is-primary" :value="progress" :max="total"></progress>
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

        <div class="columns m-0" v-show="filePicked">
            <div class="column is-4 is-offset-4">
                <div class="w100 has-text-centered p-5" v-show="processing">
                    Video is now processing.<br />
                    You can close this window and check back later.<br />
                    You will be notified when the video is ready.
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                total: 100,
                progress: 0,
                uploaded: false,
                filePicked: false,
                processing: false
            };
        },

        methods: {
            uploadVideo(video) {
                var vm = this;
                this.filePicked = true;

                ajax.post('/api/videos', video.form, {
                    onUploadProgress(e) {
                        let percent = Math.round((e.loaded * 100) / e.total);
                        vm.progress = percent;
                    }
                }).then(r => {
                    this.uploaded = true;
                    this.processing = true;
                });
            }
        },

        created() {
            //
        }
    }
</script>
