<template>
    <form :method="method" @submit.prevent="handle">
        <slot></slot>

        <b-field class="has-text-right">
            <f-form-button class="m-t-3 m-l-2" :submitting="submitting">{{ confirm }}</f-form-button>
        </b-field>
    </form>
</template>

<script>
    export default {
        props: {
            method: {
                type: String,
                default: 'post'
            },

            url: {
                type: String,
                default: ''
            },

            confirm: {
                type: String,
                default: 'Save'
            },

            submit: {
                type: Function,
                default() {}
            }
        },

        data() {
            return {
                submitting: false
            };
        },

        methods: {
            handle() {
                this.submitting = true;

                this.$emit('submit', formToObject(this.$el));

                this.submit(formToObject(this.$el));
            }
        },

        created() {
            ForiaEvent.listen('f-form-submitting', state => this.submitting = state);
        }
    }
</script>
