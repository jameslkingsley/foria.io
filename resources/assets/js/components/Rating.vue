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
            type: { type: String },
            id: { type: Number },
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
                    'is-active': this.preloaded.has_liked || false
                };
            },

            dislikeClasses() {
                return {
                    'rating': true,
                    'rating-dislike': true,
                    'is-active': this.preloaded.has_disliked || false
                };
            }
        },

        methods: {
            like() {
                if (this.preloaded.has_liked) return this.unrate();

                ajax.post('/api/ratings', {
                    type: 'like',
                    model_type: this.type,
                    model_id: this.id
                }).then(this.fetch);
            },

            dislike() {
                if (this.preloaded.has_disliked) return this.unrate();

                ajax.post('/api/ratings', {
                    type: 'dislike',
                    model_type: this.type,
                    model_id: this.id
                }).then(this.fetch);
            },

            unrate() {
                ajax.delete(`/api/ratings/${this.type}/${this.id}`)
                    .then(this.fetch);
            },

            fetch() {
                ajax.get(`/api/ratings/${this.type}/${this.id}`)
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
