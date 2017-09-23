<template>
    <div class="columns">
        <div class="column is-9">
            <div class="m-b-3">
                <div v-if="editingTopic && user.is_mine">
                    <input type="text" class="input is-pulled-left w-auto m-r-2" v-model="topic">
                    <button class="button is-primary m-r-2" @click="saveTopic">Save</button>
                    <button class="button" @click="cancelTopic">Cancel</button>
                </div>

                <div class="watch-header" v-else>
                    <div class="watch-title">
                        <span class="watch-topic">{{ topic }}</span>
                        <span class="watch-user">{{ user.name }}</span>
                    </div>

                    <div class="watch-controls">
                        <f-subscribe v-if="! user.is_mine" class="is-pulled-right m-l-2" :user="user"></f-subscribe>
                        <f-watch-follow v-if="! user.is_mine" class="is-pulled-right" :user="user"></f-watch-follow>

                        <b-dropdown v-if="user.is_mine" class="is-pulled-right" position="is-bottom-left">
                            <button class="button" slot="trigger">
                                <i class="material-icons">settings</i>
                            </button>

                            <b-dropdown-item>Subscriber mode</b-dropdown-item>
                            <b-dropdown-item @click.native="changeTopic">Change topic</b-dropdown-item>
                        </b-dropdown>

                        <button v-if="user.is_mine" :disabled="! hasWebcam" class="button is-primary is-pulled-right has-icon m-r-2" @click="startOrStop">
                            <i class="material-icons m-r-2">{{ online ? 'stop' : 'play_arrow' }}</i>
                            {{ online ? 'Stop' : 'Start' }} Broadcast
                        </button>
                    </div>
                </div>
            </div>

            <div class="watch-video-container" id="stream-publisher">
                <div v-if="! online" class="watch-placeholder">
                    <i class="material-icons">{{ offline.icon }}</i>
                    <h2>{{ offline.title }}</h2>
                    <p v-html="offline.text"></p>
                </div>
            </div>
        </div>

        <div class="column is-3">
            <div class="watch-chat-container">
                <f-watch-chat v-if="hasBroadcast" :user="user" :broadcast="stream.broadcast"></f-watch-chat>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                offline: {},
                stream: null,
                hasWebcam: false,
                editingTopic: false,
                subscriberMode: false,
                topic: this.hasBroadcast ? this.stream.broadcast.topic : 'Untitled'
            };
        },

        computed: {
            hasBroadcast() {
                return this.stream !== null
                    && 'broadcast' in this.stream;
            },

            online() {
                if (! this.hasBroadcast)
                    return false;

                return this.stream.broadcast.online;
            }
        },

        methods: {
            verifyWebcam() {
                navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: true
                })
                .then(d => this.hasWebcam = true)
                .catch(e => this.hasWebcam = false);
            },

            startOrStop() {
                if (this.online) {
                    // Stop broadcast
                    axios.delete(`/api/broadcast/stop`).then(r => {
                        this.closeStream();
                    });
                } else {
                    if (! this.hasWebcam) return;

                    // Start broadcast
                    axios.post(`/api/broadcast/start`, {
                        topic: this.topic,
                        subscriberMode: this.subscriberMode
                    }).then(r => {
                        this.openStream();
                    });
                }
            },

            openStream() {
                var vm = this;

                this.stream = new Stream(this.user.name, {
                    onError(status, text) {
                        switch (status) {
                            case 404:
                                vm.offline = {
                                    icon: 'cloud_off',
                                    title: 'Offline',
                                    text: `${vm.user.name} is offline.`
                                };

                                break;

                            case 1500:
                                vm.offline = {
                                    icon: 'error_outline',
                                    title: 'Error',
                                    text: 'No webcam detected.<br />Refresh the page once connected.'
                                };

                                break;
                        }
                    }
                });
            },

            closeStream() {
                this.stream.close();
            },

            changeTopic() {
                this.editingTopic = true;
            },

            saveTopic() {
                if (! this.hasBroadcast) {
                    this.editingTopic = false;
                    return;
                }

                axios.post('/api/broadcast/topic', { topic: this.topic }).then(r => {
                    this.editingTopic = false;
                });
            },

            cancelTopic() {
                this.editingTopic = false;
                this.topic = this.topic;
            }
        },

        created() {
            this.verifyWebcam();
            this.openStream();

            Echo.channel(`watch-${this.user.id}`)
                .listen('TopicChanged', e => {
                    this.topic = e.topic;
                })
                .listen('NewSubscription', e => {
                    //
                });
        }
    }
</script>
