<template>
    <form :method="method" @submit.prevent="handle">
        <slot></slot>

        <f-form-button class="is-pulled-right m-t-3 m-l-2" :submitting="submitting">{{ confirm }}</f-form-button>
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

                this.submit();
            }
        },

        created() {
            Event.listen('f-form-submitting', state => this.submitting = state);
        }
    }
</script>
