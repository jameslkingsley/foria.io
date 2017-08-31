<template>
    <div class="w100 has-text-centered p-5">
        <input ref="input" type="file" accept="video/*" @change="onChange" style="display:none">

        <p class="m-b-3" v-show="isUploading">
            Your video is being uploaded.
            <br />
            Do not close your browser.
        </p>

        <p class="m-b-3" v-show="! isUploading">
            You must own the copyright to the video.
            <br />
            By uploading a video you agree to our terms of use.
        </p>

        <button :class="buttonClasses" @click.prevent="openFinder">Choose Video</button>
    </div>
</template>

<script>
    export default {
        props: {
            uploaded: {
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                uploadedInternal: this.uploaded,
                hasPickedFile: false
            };
        },

        watch: {
            uploaded(value) {
                this.uploadedInternal = value;
            }
        },

        computed: {
            isUploading() {
                return this.hasPickedFile && ! this.uploadedInternal;
            },

            buttonClasses() {
                return {
                    'button': true,
                    'is-primary': true,
                    'is-loading': this.isUploading
                };
            }
        },

        methods: {
            onChange(e) {
                if (! e.target.files.length) return;

                this.hasPickedFile = true;

                let file = e.target.files[0];
                let form = new FormData();

                form.append(
                    this.$el.getAttribute('name'),
                    file
                );

                this.$emit('loaded', { file, form });
            },

            openFinder() {
                this.$refs.input.click();
            }
        }
    }
</script>
