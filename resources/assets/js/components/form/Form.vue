<template>
    <form :method="method" @submit.prevent="handle">
        <span v-if="title.length" class="f-form-title title has-text-centered">
            {{ title }}
        </span>

        <div :class="wrapperClasses">
            <slot></slot>
        </div>

        <b-field class="f-form-footer has-text-right" :style="footerStyle">
            <slot name="footer"></slot>
            <f-form-button class="m-0 is-pulled-right" :style="submitStyle" :submitting="submitting">{{ confirm }}</f-form-button>
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

            title: {
                type: String,
                default: ''
            },

            submit: {
                type: Function,
                default() {}
            },

            footerStyle: {
                type: Object,
                default: () => { return {} }
            },

            submitStyle: {
                type: Object,
                default: () => { return {} }
            }
        },

        data() {
            return {
                submitting: false
            };
        },

        computed: {
            wrapperClasses() {
                return {
                    'p-3': this.title.length,
                    'is-pulled-left': this.title.length,
                    'w100': this.title.length
                };
            }
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
