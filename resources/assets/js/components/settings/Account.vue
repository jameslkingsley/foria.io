<template>
    <div v-if="loaded">
        <f-modal-form title="Change Username" confirm="Save Changes" :submit="changeUsername" :active.sync="isChangingUsername">
            <input type="hidden" name="method" value="username">
            <b-field label="New Username">
                <b-input name="name"></b-input>
            </b-field>
        </f-modal-form>

        <h3 class="settings-title">Username</h3>
        <p>Your username is <strong>{{ user.name }}</strong>. Changing your username will break links to your old username (such as <code>/watch/OldUsername</code>).</p>
        <button class="button is-primary m-t-2" @click="isChangingUsername = true">Change Username</button>

        <hr />

        <f-modal-form title="Change Email Address" confirm="Save Changes" :submit="changeEmail" :active.sync="isChangingEmail">
            <input type="hidden" name="method" value="email">

            <b-field label="New Email Address">
                <b-input type="email" name="email"></b-input>
            </b-field>

            <b-field label="Confirm New Email Address">
                <b-input type="email" name="confirm_email"></b-input>
            </b-field>
        </f-modal-form>

        <h3 class="settings-title">Email Address</h3>
        <p>Your email address is <strong>{{ user.email }}</strong>.</p>
        <button class="button is-primary m-t-2" @click="isChangingEmail = true">Change Email Address</button>

        <hr />

        <h3 class="settings-title">Password</h3>
        <button class="button is-primary m-t-2">Change Password</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: null,
                loaded: false,
                isChangingEmail: false,
                isChangingUsername: false,
                isChangingPassword: false
            };
        },

        methods: {
            getUser() {
                axios.get('/settings/account').then(r => {
                    this.user = r.data.user;
                    this.loaded = true;
                });
            },

            changeUsername(data) {
                return axios.post('/settings/account', data).then(r => {
                    this.isChangingUsername = false;

                    this.$toast.open({
                        message: 'Username Changed',
                        type: 'is-success',
                        duration: 4000
                    });

                    this.getUser();
                }).catch(({ response }) => {
                    this.$toast.open({
                        message: response.data.errors.name[0],
                        type: 'is-danger',
                        duration: 4000
                    });
                });
            },

            changeEmail(data) {
                return axios.post('/settings/account', data).then(r => {
                    this.isChangingEmail = false;

                    this.$toast.open({
                        message: 'Email Changed',
                        type: 'is-success',
                        duration: 4000
                    });

                    this.getUser();
                }).catch(({ response }) => {
                    this.$toast.open({
                        message: response.data.errors.email[0],
                        type: 'is-danger',
                        duration: 4000
                    });
                });
            }
        },

        mounted() {
            this.getUser();
        }
    }
</script>
