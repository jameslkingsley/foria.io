<template>
    <div class="columns">
        <div class="column is-sidebar settings-nav">
            <!-- TODO (Video Manager, Stats) -->
        </div>

        <div class="column settings-content card">
            <h3 class="settings-title">
                Edit Video
            </h3>

            <f-form confirm="Save Changes &amp; Publish" @submit="submit">
                <b-field label="Name">
                    <b-input name="name" :value="video.name"></b-input>
                </b-field>

                <b-field label="Token Price">
                    <b-input name="token_price" type="number" :value="video.token_price"></b-input>
                </b-field>

                <div class="field">
                    <b-checkbox name="subscriber_only" v-model="subscriberOnlyChecked">Subscriber Only</b-checkbox>
                </div>
            </f-form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['video'],

        data() {
            return {
                subscriberOnlyChecked: this.video.subscriber_only
            };
        },

        methods: {
            submit(data) {
                axios.post(`/api/videos/${this.video.id}`, data)
                    .then(r => {
                        alert('Changes Saved');
                    });
            }
        }
    }
</script>
