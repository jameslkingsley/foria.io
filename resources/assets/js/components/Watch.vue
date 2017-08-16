<template>
    <div class="columns">
        <div class="column is-9">
            <div class="m-b-3">
                <div v-if="editingTopic && user.is_mine">
                    <input type="text" v-model="topic">
                    <button class="button" @click="saveTopic">Save</button>
                    <button class="button" @click="cancelTopic">Cancel</button>
                </div>

                <div class="watch-header" v-else>
                    <div class="watch-title">
                        <span class="watch-topic">{{ topic }}</span>
                        <span class="watch-user">{{ user.name }}</span>
                    </div>

                    <div class="watch-controls">
                        <f-subscribe v-if="! user.is_mine" class="is-pulled-right m-l-1" :user="user"></f-subscribe>
                        <f-follow v-if="! user.is_mine" class="is-pulled-right" :user="user"></f-follow>

                        <b-dropdown v-if="user.is_mine" class="is-pulled-right" position="is-bottom-left">
                            <button class="button" slot="trigger">
                                <i class="material-icons">settings</i>
                            </button>

                            <b-dropdown-item>Subscriber mode</b-dropdown-item>
                            <b-dropdown-item @click.native="changeTopic">Change topic</b-dropdown-item>
                        </b-dropdown>

                        <button v-if="user.is_mine" class="button is-primary is-pulled-right has-icon m-r-1" @click="online ? stop : start">
                            <i class="material-icons m-r-2">{{ online ? 'stop' : 'play_arrow' }}</i>
                            {{ online ? 'Stop' : 'Start' }} Broadcast
                        </button>
                    </div>
                </div>
            </div>

            <div class="watch-video-container" id="stream-publisher"></div>
        </div>

        <div class="column is-3">
            <div class="watch-chat-container"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user', 'broadcast'],

        data() {
            return {
                online: false,
                editingTopic: false,
                hasBroadcast: this.broadcast !== null,
                topic: this.broadcast ? this.broadcast.topic : 'Untitled'
            };
        },

        methods: {
            start() {
                axios.post(`/api/broadcast/start`).then(r => {
                    this.online = true;
                    this.openStream();
                });
            },

            stop() {
                axios.delete(`/api/broadcast/stop`).then(r => {
                    this.online = false;
                    this.openStream();
                });
            },

            openStream() {
                return new Stream(this.user.id);
            },

            changeTopic() {
                this.editingTopic = true;
            },

            saveTopic() {
                axios.post('/api/broadcast/topic', { topic: this.topic }).then(r => {
                    this.editingTopic = false;
                });
            },

            cancelTopic() {
                this.editingTopic = false;
                this.topic = this.broadcast.topic;
            }
        },

        created() {
            this.openStream();
        }
    }
</script>
