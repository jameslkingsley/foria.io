<template>
    <button class="button" @click="handle" v-if="state != null" :title="text">
        <i class="material-icons m-r-2">{{ icon }}</i>
        {{ count }}
    </button>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                state: null,
                count: this.user.follower_count
            };
        },

        computed: {
            icon() {
                return this.state ? 'favorite_border' : 'favorite';
            },

            text() {
                return this.state ? 'Unfollow' : 'Follow';
            }
        },

        methods: {
            handle() {
                return axios[
                    (this.state ? 'delete' : 'post')
                ](`/api/follow/${this.user.name}`).then(this.fetch);
            },

            fetch() {
                axios.get(`/api/follow/${this.user.name}`).then(r => {
                    this.state = r.data;
                });
            }
        },

        created() {
            this.fetch();

            Echo.channel(`followed-${this.user.id}`)
                .listen('Followed', e => {
                    this.count = e.count;
                })
                .listen('Unfollowed', e => {
                    this.count = e.count;
                });
        }
    }
</script>
