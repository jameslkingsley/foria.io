<template>
    <div class="tile is-ancestor">
        <div class="tile is-parent">
            <article class="tile is-child box">
                <p class="title">
                    {{ user.name }}
                    <button class="button is-primary is-pulled-right" @click="start">
                        Start Broadcasting
                    </button>
                </p>

                <div class="columns">
                    <div class="column is-9" id="stream-publisher"></div>

                    <div class="column is-3">
                        Chat
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

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
