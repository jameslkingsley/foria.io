<template>
    <div>
        <h1 class="subtitle m-b-3">Comments</h1>

        <form method="post" @submit.prevent="post">
            <input class="input" name="body" v-model="body" placeholder="Type a comment...">
        </form>

        <p v-if="! loaded" class="m-t-3">Loading...</p>

        <ul class="comments m-t-3" v-if="loaded">
            <li class="comment" v-for="comment in comments">
                <div class="comment-details">
                    <span class="comment-author">
                        {{ comment.user.name }}
                    </span>

                    &middot;

                    <span class="comment-timestamp">
                        {{ comment.created_at | fromnow }}
                    </span>
                </div>

                <div class="comment-body">
                    {{ comment.body }}
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            reference: { type: String },
            preload: { type: Array, default: null }
        },

        data() {
            return {
                loaded: this.preload !== null,
                comments: this.preload,
                body: ''
            };
        },

        methods: {
            post() {
                return ajax.post(`/api/comments/${this.reference}`, { body: this.body })
                    .then(r => {
                        this.body = '';
                        this.comments.unshift(r.data);
                    });
            },

            fetch() {
                return ajax.get(`/api/comments/${this.reference}`)
                    .then(r => {
                        this.loaded = true;
                        this.comments = r.data;
                    });
            }
        },

        created() {
            if (this.preload === null) {
                this.fetch();
            }
        }
    }
</script>
