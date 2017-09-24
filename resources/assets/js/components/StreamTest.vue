<template>
    <div class="card">
        <video ref="input" controls />
        <video ref="output" controls />
    </div>
</template>

<script>
    export default {
        mounted() {
            const stream = new LiveStream;

            console.log(stream);
        }
    }
</script>
