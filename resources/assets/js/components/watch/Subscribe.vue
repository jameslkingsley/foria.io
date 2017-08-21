<template>
    <button class="button is-primary" @click="handle" v-if="subscribed != null">
        <i class="material-icons m-r-2">{{ icon }}</i>
        {{ text }}
    </button>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                subscribed: false
            };
        },

        computed: {
            icon() {
                return this.subscribed ? 'close' : 'check';
            },

            text() {
                return this.subscribed ? 'Subscription' : 'Subscribe';
            }
        },

        methods: {
            handle() {
                if (this.subscribed) {
                    axios.delete(`/api/subscription/${this.user.id}`).then(r => {
                        //
                    });
                } else {
                    axios.post('/api/subscription', { user_id: this.user.id }).then(r => {
                        //
                    });
                }
            },

            fetch() {
                axios.get(`/api/subscription/${this.user.id}`).then(r => {
                    this.subscribed = r.data;
                });
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
