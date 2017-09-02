<template>
    <div class="card p-3">
        <h3 class="subtitle m-b-2">
            Videos
            <a href="/videos/new" class="button is-primary is-pulled-right">Upload Video</a>
        </h3>

        <f-video-list :user="user"></f-video-list>
    </div>
</template>

<script>
    export default {
        props: ['user']
    }
</script>
