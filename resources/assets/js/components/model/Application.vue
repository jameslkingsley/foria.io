<template>
    <div class="grid grid-gap-1 grid-narrow">
        <div>
            <a href="/settings" class="flat">
                <i class="material-icons">keyboard_backspace</i>
                Settings
            </a>
        </div>

        <div class="card">
            <f-form v-show="showDetailsForm" title="Become a Model" confirm="Continue" @submit="formContinue">
                <b-field label="Full Legal Name">
                    <b-input required v-model="form.full_name" name="full_name"></b-input>
                </b-field>

                <b-field label="Gender">
                    <b-select v-model="form.gender" placeholder="Select your gender" expanded>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </b-select>
                </b-field>

                <b-field v-show="form.gender === 'female'" label="Maiden Name">
                    <b-input v-model="form.maiden_name" name="maiden_name"></b-input>
                </b-field>

                <b-field label="List of Used Nicknames">
                    <b-input required v-model="form.nicknames" name="nicknames"></b-input>
                </b-field>

                <b-field label="Date of Birth">
                    <b-datepicker
                        required
                        v-model="form.date_of_birth"
                        placeholder="Click to select..."
                        icon="today">
                    </b-datepicker>
                </b-field>

                <b-field label="Country of Residence">
                    <b-input required v-model="form.country" name="country"></b-input>
                </b-field>
            </f-form>

            <f-form v-show="showIdentityForm" title="Become a Model" confirm="Complete" @submit="formComplete">
                <b-field label="Picture of your valid, government-issued ID">
                    <f-form-image-upload
                        name="photo_id"
                        @loaded="selectPhotoId">
                    </f-form-image-upload>
                </b-field>

                <b-field label="Picture of yourself holding the ID">
                    <f-form-image-upload
                        name="photo_self"
                        @loaded="selectPhotoSelf">
                    </f-form-image-upload>
                </b-field>
            </f-form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                photoForm: new FormData(),
                showDetailsForm: true,
                showIdentityForm: false,
                form: {
                    gender: '',
                    country: '',
                    nicknames: '',
                    full_name: '',
                    maiden_name: '',
                    date_of_birth: null
                }
            };
        },

        methods: {
            formContinue() {
                ajax.post('/api/model-application', this.form)
                    .then(r => {
                        this.showDetailsForm = false;
                        this.showIdentityForm = true;
                        this.photoForm.append('application_id', r.data.id);
                    });
            },

            formComplete() {
                ajax.post(`/api/model-application/id`, this.photoForm)
                    .then(r => {
                        this.$toast.open({
                            message: 'Uploaded',
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            selectPhotoId(photo) {
                this.photoForm.append('photo_id', photo.file);
            },

            selectPhotoSelf(photo) {
                this.photoForm.append('photo_self', photo.file);
            }
        }
    }
</script>
