<template>
    <a :class="itemClasses" @click.prevent="click">
        <i :class="iconClasses" :style="iconStyle" v-text="icon"></i>
        <span v-text="text"></span>
        <p v-text="subtext"></p>
    </a>
</template>

<script>
    export default {
        props: {
            text: { type: String, default: '' },
            icon: { type: String, default: '' },
            subtext: { type: String, default: '' },
            thumbnail: { type: String, default: '' }
        },

        computed: {
            itemClasses() {
                return {
                    'active': true
                };
            },

            iconStyle() {
                if (!this.thumbnail.length) return {};

                return {
                    'background-image': `url(${this.thumbnail})`
                };
            },

            iconClasses() {
                return {
                    'material-icon': !this.thumbnail.length
                };
            }
        },

        methods: {
            click() {
                this.$emit('click');
            }
        }
    }
</script>
