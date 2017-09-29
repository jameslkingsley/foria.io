<template>
    <div class="card p-3">
        <button class="button m-r-2" @click="broadcast">
            Broadcast
        </button>

        <button class="button m-r-2" @click="view">
            View
        </button>

        <button class="button" @click="stop">
            Stop
        </button>

        <video id="red5pro-publisher" ref="broadcaster" controls autoplay muted width="100%" height="640px"></video>
        <video id="red5pro-subscriber" ref="viewer" controls autoplay muted width="100%" height="640px"></video>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                stream: null
            };
        },

        methods: {
            broadcast() {
                this.stream = LiveStream.Publisher({
                    //
                });

                console.log(this.stream);
            },

            view() {
                this.stream = LiveStream.Subscriber({
                    //
                });

                console.log(this.stream);
            },

            stop() {
                this.stream.stop();

                this.stream = null;
            }
        },

        mounted() {
            //
        }
    }
</script>
