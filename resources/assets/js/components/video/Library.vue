<template>
    <div>
        <!-- <div v-if="true"> -->
            <h3 class="section-title">Your Feed</h3>

            <div class="video-list" style="margin-bottom: 7rem">
                <f-video-item
                    v-for="(video, index) in feed"
                    :key="index"
                    :video="video">
                </f-video-item>
            </div>
        <!-- </div> -->

        <h3 class="section-title">Recent</h3>

        <div class="video-list">
            <f-video-item
                v-for="(video, index) in recent"
                :key="index"
                :video="video">
            </f-video-item>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            // Most viewed this week
            trending: { type: Array, default: [] },

            // Recent videos from followed/subscribed models
            feed: { type: Array, default: [] },

            // Recently uploaded by anyone
            recent: { type: Array, default: [] }
        }
    }
</script>
