<template>
    <div class="f-vertical-nav">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log(this.$children);
        }
    }
</script>
