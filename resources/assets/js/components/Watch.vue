<template>
    <div class="columns">
        <div class="column is-9">
            <p class="title">
                {{ user.name }}

                <f-subscribe v-if="! user.is_mine" class="is-pulled-right m-l-1" :user="user"></f-subscribe>
                <f-follow v-if="! user.is_mine" class="is-pulled-right" :user="user"></f-follow>

                <button v-if="user.is_mine" class="button is-primary is-pulled-right" @click="start">
                    Start Broadcasting
                </button>
            </p>

            <div class="watch-video-container" id="stream-publisher"></div>
        </div>

        <div class="column is-3">
            <div class="watch-chat-container"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                //
            };
        },

        methods: {
            start() {
                axios.post(`/watch/${this.user.id}/start`)
                    .then(this.open);
            },

            open() {
                return new Stream(this.user.id);
            }
        },

        created() {
            this.open();
        }
    }
</script>
