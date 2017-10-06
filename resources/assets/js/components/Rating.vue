<template>
    <div v-if="loaded">
        <button :class="dislikeClasses" @click.prevent="dislike">
            <i class="material-icons">thumb_down</i>
            {{ preloaded.dislikes }}
        </button>

        <button :class="likeClasses" @click.prevent="like">
            <i class="material-icons">thumb_up</i>
            {{ preloaded.likes }}
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            reference: { type: String },
            preload: { type: Object, default: null }
        },

        data() {
            return {
                loaded: false,
                preloaded: this.preload || {
                    likes: 0,
                    dislikes: 0,
                    has_liked: false,
                    has_disliked: false
                }
            };
        },

        computed: {
            likeClasses() {
                return {
                    'rating': true,
                    'rating-like': true,
                    'cursor-default': !this.authorized,
                    'is-active': this.preloaded.has_liked || false
                };
            },

            dislikeClasses() {
                return {
                    'rating': true,
                    'rating-dislike': true,
                    'cursor-default': !this.authorized,
                    'is-active': this.preloaded.has_disliked || false
                };
            },

            authorized() {
                return Foria.user !== null;
            }
        },

        methods: {
            like() {
                if (!this.authorized) return;

                if (this.preloaded.has_liked) return this.unrate();

                ajax.post(`/api/ratings/${this.reference}`, {
                    type: 'like'
                }).then(this.fetch);
            },

            dislike() {
                if (!this.authorized) return;

                if (this.preloaded.has_disliked) return this.unrate();

                ajax.post(`/api/ratings/${this.reference}`, {
                    type: 'dislike'
                }).then(this.fetch);
            },

            unrate() {
                if (!this.authorized) return;

                ajax.delete(`/api/ratings/${this.reference}`)
                    .then(this.fetch);
            },

            fetch() {
                ajax.get(`/api/ratings/${this.reference}`)
                    .then(r => {
                        this.preloaded = r.data;
                        this.loaded = true;
                    });
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
