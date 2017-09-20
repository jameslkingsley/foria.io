<template>
    <div :class="classes" :style="styles"></div>
</template>

<script>
    export default {
        props: ['user'],

        computed: {
            classes() {
                return {
                    'profile-avatar': true
                };
            },

            styles() {
                return {
                    'background-image': `url(${this.user.avatar_url})`
                };
            }
        }
    }
</script>
