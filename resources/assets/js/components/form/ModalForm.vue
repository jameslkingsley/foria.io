<template>
    <div :class="classes">
        <div class="modal-background" @click="close"></div>

        <div class="modal-card">
            <form ref="form" :method="method" @submit.prevent="handle">
                <header class="modal-card-head">
                    <p class="modal-card-title">{{ title }}</p>
                    <i class="material-icons" @click="close">close</i>
                </header>

                <section class="modal-card-body">
                    <slot></slot>
                </section>

                <footer class="modal-card-foot">
                    <button class="button" @click.prevent="close">Cancel</button>
                    <f-form-button :submitting="submitting">{{ confirm }}</f-form-button>
                </footer>
            </form>
        </div>

        <button class="modal-close is-large" aria-label="close" @click="close"></button>
    </div>
</template>

<script>
    export default {
        props: {
            active: {
                type: Boolean,
                default: false
            },

            title: {
                type: String,
                default: ''
            },

            method: {
                type: String,
                default: 'post'
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
                visible: this.active,
                submitting: false
            };
        },

        watch: {
            active(value) {
                this.visible = value;
            }
        },

        computed: {
            classes() {
                return {
                    'modal': true,
                    'is-active': this.visible
                };
            }
        },

        methods: {
            close() {
                this.$emit('update:active', false);
                this.visible = false;
            },

            handle() {
                this.submitting = true;

                this.submit(formToObject(this.$refs.form));
            }
        },

        created() {
            ForiaEvent.listen('f-form-submitting', state => this.submitting = state);
        }
    }
</script>
