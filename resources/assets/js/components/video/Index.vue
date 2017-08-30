<template>
    <div class="card p-3">
        <h1 class="title">{{ video.name }}</h1>

        <video class="video-js" controls preload="auto" width="960" height="540" data-setup="{}">
            <source :src="videoUrl" type="video/mp4">
        </video>
    </div>
</template>

<script>
    export default {
        props: ['video', 'videoUrl'],

        created() {
            console.log(this.videoUrl);
        }
    }
</script>
