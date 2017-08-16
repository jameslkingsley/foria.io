<template>
    <div>
        <f-broadcast-tile
            v-for="(broadcast, index) in broadcasts"
            :key="index"
            :broadcast="broadcast">
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
            axios.get('/api/broadcast-query').then(
                r => this.broadcasts = r.data
            );
        }
    }
</script>
