<template>
    <span class="watch-topic">
        <!-- Edit -->
        <button v-if="editable && ! editing" @click.prevent="edit" class="watch-topic-control watch-topic-edit">
            <i class="material-icons">mode_edit</i>
        </button>

        <!-- Cancel -->
        <button v-if="editing" @click.prevent="cancel" class="watch-topic-control watch-topic-cancel">
            <i class="material-icons">close</i>
        </button>

        <!-- Save -->
        <button v-if="editing" @click.prevent="save" class="watch-topic-control watch-topic-save">
            <i class="material-icons">check</i>
        </button>

        <!-- Text -->
        <span v-if="! editing">{{ topic }}</span>

        <!-- Input -->
        <input v-focus v-if="editing" v-model="topic" class="input is-pulled-left w100">
    </span>
</template>

<script>
    export default {
        props: {
            text: { type: String, default: 'Untitled' },
            editable: { type: Boolean, default: false }
        },

        data() {
            return {
                topic: this.text,
                editing: false
            };
        },

        directives: {
            focus: {
                inserted(el) {
                    el.focus();
                }
            }
        },

        methods: {
            save() {
                return ajax.patch('/api/broadcast', { topic: this.topic })
                    .then(r => this.editing = false);
            },

            cancel() {
                this.topic = this.text;
                this.editing = false;
            },

            edit() {
                this.editing = true;
            }
        }
    }
</script>
