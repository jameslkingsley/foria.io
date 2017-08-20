<template>
    <div :class="classes">
        <div class="modal-background" @click="close"></div>

        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ title }}</p>
                <i class="material-icons" @click="close">close</i>
            </header>

            <section class="modal-card-body">
                <slot name="content"></slot>
            </section>

            <footer class="modal-card-foot">
                <slot name="footer"></slot>
            </footer>
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
            }
        },

        data() {
            return {
                visible: this.active
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
            }
        },

        created() {
            //
        }
    }
</script>
