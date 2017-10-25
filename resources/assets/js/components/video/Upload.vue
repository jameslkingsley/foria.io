<template>
    <div class="grid grid-gap-1 grid-small">
        <div>
            <a href="/videos/manager" class="flat">
                <i class="material-icons">keyboard_backspace</i>
                Video Manager
            </a>
        </div>

        <div class="card p-3">
            <form enctype="multipart/form-data" v-show="! processing">
                <div class="grid grid-small" v-show="filePicked">
                    <div class="has-text-centered p-5">
                        <h2 class="subtitle">{{ progress }}%</h2>
                        <progress class="progress is-primary" :value="progress" :max="total"></progress>
                    </div>
                </div>

                <f-form-video-upload
                    name="video"
                    v-show="! filePicked"
                    :uploaded.sync="uploaded"
                    @loaded="uploadVideo">
                </f-form-video-upload>
            </form>

            <div class="w100 has-text-centered p-5" v-show="filePicked && processing">
                <i class="material-icons text-large has-text-warning">lens</i>
                <h3 class="subtitle m-b-1 m-t-3">Your video is now processing</h3>
                <small>You will be notified when the video is ready.</small>
                <br />
                <small>You can now close this window.</small>
            </div>
        </div>

        <div class="card p-3">
            <small>By submitting your videos to Foria, you acknowledge that you agree to Foria's <a target="_newtab">Terms of Service</a>.</small>
            <small>Please be sure not to violate others' copyright or privacy rights.</small>
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
