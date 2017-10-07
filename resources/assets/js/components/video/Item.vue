<template>
    <div class="video-item">
        <a :href="video.url" class="video">
            <div class="video-thumbnail" :style="getStyle(video)">
                <span class="video-duration">
                    {{ video.duration | duration }}
                </span>

                <span class="video-hd">
                    {{ (video.height >= 720) ? 'HD' : '' }}
                    {{ video.height }}P
                </span>
            </div>

            <span class="video-name" v-text="video.name"></span>

            <span class="video-meta">
                {{ video.user.name }}
                &middot;
                {{ video.created_at | fromnow }}
            </span>
        </a>
    </div>
</template>

<script>
    export default {
        props: ['video'],

        methods: {
            getStyle(video) {
                return {
                    'background-image': `url(${video.thumbnail})`
                };
            }
        }
    }
</script>
