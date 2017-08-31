<template>
    <div class="card p-3">
        <h1 class="title">
            {{ video.name }}
            <a :href="video.edit_url" class="button is-pulled-right" v-if="video.is_mine">Edit</a>
        </h1>

        <video class="video-js" controls preload="auto" width="960" height="540" data-setup="{}">
            <source :src="video.stream_url" type="video/mp4">
        </video>
    </div>
</template>

<script>
    export default {
        props: ['video']
    }
</script>
