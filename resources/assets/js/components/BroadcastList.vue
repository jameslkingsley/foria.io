<template>
    <div>
        <f-broadcast-tile
            v-for="(broadcast, index) in broadcasts"
            :key="index"
            :broadcaster="broadcast">
        </f-broadcast-tile>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                broadcasts: []
            };
        },

        created() {
            axios.get('/broadcasts').then(
                r => this.broadcasts = r.data
            );
        }
    }
</script>
