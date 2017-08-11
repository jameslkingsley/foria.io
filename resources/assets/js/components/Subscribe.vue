<template>
    <button class="button is-primary" @click="handle" v-if="state != null">
        <i class="material-icons m-r-2">{{ icon }}</i>
        {{ text }}
    </button>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                state: false
            };
        },

        computed: {
            icon() {
                return this.state ? 'close' : 'check';
            },

            text() {
                return this.state ? 'Subscription' : 'Subscribe';
            }
        },

        methods: {
            handle() {
                return axios[
                    (this.state ? 'delete' : 'post')
                ](`/subscribe/${this.user.id}`).then(this.fetch);
            },

            fetch() {
                axios.get(`/subscribe/${this.user.id}`).then(r => {
                    this.state = r.data;
                });
            }
        },

        created() {
            // this.fetch();
        }
    }
</script>
