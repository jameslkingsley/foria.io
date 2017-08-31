<template>
    <div class="card p-3">
        <form enctype="multipart/form-data">
            <f-form-video-upload
                name="video"
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
                uploaded: false
            };
        },

        methods: {
            uploadVideo(video) {
                axios.post(`/api/videos`, video.form)
                    .then(r => {
                        this.uploaded = true;
                        window.location.href = r.data.edit_url;
                    });
            }
        }
    }
</script>
