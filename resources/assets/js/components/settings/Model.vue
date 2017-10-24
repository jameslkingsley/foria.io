<template>
    <div>
        <h3 class="settings-title">Model Profile</h3>

        <div v-if="! user.is_model">
            <p>
                You are not an approved model. You can fill out the form below to request to become a model. The information you submit is private and will be temporarily stored to verify your identity.
            </p>

            <hr />

            <f-form confirm="Submit Application" :submit="submitRequest">
                <b-field label="Full Legal Name">
                    <b-input name="full_name"></b-input>
                </b-field>

                <b-field label="Date of Birth">
                    <b-datepicker
                        placeholder="Click to select..."
                        icon="today">
                    </b-datepicker>
                </b-field>

                <b-field label="About Yourself">
                    <b-input type="textarea" name="about"></b-input>
                </b-field>

                <b-field label="Proof of Age">
                    <b-upload v-model="dropFiles" multiple drag-drop>
                        <section class="section">
                            <div class="content has-text-centered">
                                <p>
                                    <b-icon
                                        icon="file_upload"
                                        size="is-large">
                                    </b-icon>
                                </p>
                                <p>Drop your files here or click to upload</p>
                            </div>
                        </section>
                    </b-upload>
                </b-field>

                <div class="tags">
                    <span v-for="(file, index) in dropFiles"
                        :key="index"
                        class="tag is-primary" >
                        {{ file.name }}
                        <button class="delete is-small"
                            type="button"
                            @click="deleteDropFile(index)">
                        </button>
                    </span>
                </div>
            </f-form>
        </div>

        <div v-else>
            <h4 class="settings-subtitle">Avatar</h4>

            <img class="image is-128x128 is-pulled-left m-r-3" :src="avatar">

            <form>
                <b-field class="is-pulled-left">
                    <f-form-image-upload
                        name="avatar"
                        @loaded="uploadAvatar">
                    </f-form-image-upload>
                </b-field>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                dropFiles: [],
                avatar: this.user.avatar_url
            };
        },

        methods: {
            deleteDropFile(index) {
                this.dropFiles.splice(index, 1);
            },

            submitRequest(data) {
                console.log(data);
            },

            uploadAvatar(avatar) {
                this.avatar = avatar.src;

                axios.post('/api/settings/avatar', avatar.form)
                    .then(r => {
                        this.$toast.open({
                            message: 'Avatar Uploaded',
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            fetch() {
                axios.get('/settings/model').then(r => {
                    //
                });
            }
        },

        mounted() {
            if (this.user.is_model) {
                this.fetch();
            }
        }
    }
</script>
