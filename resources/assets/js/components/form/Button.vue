<template>
    <button :type="type" :class="classes" :disabled="submitting">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'submit'
            },

            submitting: {
                type: Boolean,
                default: false
            }
        },

        computed: {
            classes() {
                return {
                    'button': true,
                    'is-primary': true,
                    'is-loading': this.submitting
                };
            }
        }
    }
</script>
