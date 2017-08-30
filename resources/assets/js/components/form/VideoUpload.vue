<template>
    <input type="file" accept="video/*" @change="onChange">
</template>

<script>
    export default {
        methods: {
            onChange(e) {
                if (! e.target.files.length) return;

                let file = e.target.files[0];
                let form = new FormData();

                form.append(
                    this.$el.getAttribute('name'),
                    file
                );

                this.$emit('loaded', { file, form });
            }
        }
    }
</script>
