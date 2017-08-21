<template>
    <div>
        <h3 class="settings-title">Model Status</h3>

        <div v-if="! user.is_model">
            <p>
                You are not an approved model. You can fill out the form below to request to become a model. The information you submit is private and will be temporarily stored to verify your identity.
            </p>

            <hr />

            <f-form confirm="Submit Application" :submit="submitRequest">
                <b-field label="Full Legal Name">
                    <b-input name="full_name"></b-input>
                </b-field>

                <b-field label="About Yourself">
                    <b-input type="textarea" name="about"></b-input>
                </b-field>
            </f-form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        methods: {
            submitRequest(data) {
                console.log(data);
            }
        }
    }
</script>
