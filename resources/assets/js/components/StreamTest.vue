<template>
    <div class="card">
        <video ref="input" controls />
        <video ref="output" controls />
    </div>
</template>

<script>
    export default {
        mounted() {
            const broadcaster = new Broadcaster({
                input: this.$refs.input,
                output: this.$refs.output
            });

            broadcaster.start();
        }
    }
</script>
