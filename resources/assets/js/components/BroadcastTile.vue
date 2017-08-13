<template>
    <a :href="broadcaster.watch_url" class="box broadcast-tile">
        <div class="broadcast-details">
            <span class="broadcast-title">{{ broadcaster.name }}</span>
            <span class="broadcast-viewers">2,346</span>
            <span class="broadcast-topic">A topic the broadcaster can set</span>

            <div class="broadcast-controls"></div>
        </div>
    </a>
</template>

<script>
    export default {
        props: ['broadcaster'],

        data() {
            return {
                user: Foria.user
            };
        }
    }
</script>
