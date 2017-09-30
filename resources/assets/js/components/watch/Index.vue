<template>
    <div class="columns">
        <div class="column is-9">
            <div class="m-b-3">
                <div class="watch-header">
                    <div class="watch-title">
                        <f-watch-topic :text="topic" :editable="user.is_mine"></f-watch-topic>
                        <span class="watch-user">{{ user.name }}</span>
                    </div>

                    <div class="watch-controls">
                        <f-subscribe v-if="! user.is_mine" class="is-pulled-right m-l-2" :user="user"></f-subscribe>
                        <f-follow v-if="! user.is_mine" class="is-pulled-right" :user="user"></f-follow>

                        <b-dropdown v-if="user.is_mine" class="is-pulled-right" position="is-bottom-left">
                            <button class="button" slot="trigger">
                                <i class="material-icons">settings</i>
                            </button>

                            <b-dropdown-item>Subscriber mode</b-dropdown-item>
                            <b-dropdown-item @click.native="changeTopic">Change topic</b-dropdown-item>
                        </b-dropdown>

                        <button v-if="user.is_mine" class="button is-primary is-pulled-right has-icon m-r-2" @click="startOrStop">
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

                <div>
                    <video
                        loop
                        controls
                        autoplay
                        preload="auto"
                        width="100%"
                        height="540"
                        id="watch-video-driver"
                        :class="videoClasses"
                        :poster="user.avatar_url"
                        :data-setup="videoSetupJson">
                    </video>
                </div>
            </div>
        </div>

        <div class="column is-3">
            <div class="watch-chat-container">
                <f-watch-chat v-if="hasBroadcast" :user="user" :broadcast="broadcast"></f-watch-chat>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user', 'broadcast'],

        data() {
            return {
                offline: {},
                stream: null,
                subscriberMode: false,
                topic: this.broadcast ? this.broadcast.topic : 'Untitled',
                online: this.hasBroadcast ? this.broadcast.online : false,
                videoSetup: {
                    fluid: true
                }
            };
        },

        computed: {
            hasBroadcast() {
                return this.broadcast !== null;
            },

            videoClasses() {
                return {
                    'video-js': true,
                    'vjs-default-skin': true,
                    'vjs-big-play-centered': true,
                    'vjs-16-9': true
                };
            },

            videoSetupJson() {
                return JSON.stringify(this.videoSetup);
            }
        },

        methods: {
            startOrStop() {
                if (this.online) {
                    // Stop broadcast
                    // console.log(this.stream.driver.unpublish);
                    this.stream.stop();
                } else {
                    // Start broadcast
                    this.publish();
                }
            },

            publish() {
                var vm = this;

                this.stream = LiveStream.Publisher({
                    driver: {
                        streamName: this.user.name,
                        mediaElementId: 'watch-video-driver'
                    },

                    onBroadcasting(broadcast) {
                        this.online = broadcast.online;
                    },

                    onFail() {
                        vm.offline = {
                            icon: 'error_outline',
                            title: 'Error',
                            text: 'Failed to start live stream.'
                        };
                    }
                });
            },

            subscribe() {
                this.stream = LiveStream.Subscriber({
                    driver: {
                        streamName: this.user.name,
                        mediaElementId: 'watch-video-driver'
                    }
                });
            }
        },

        mounted() {
            if (this.user.is_mine) {
                if (this.broadcast.online) {
                    this.publish();
                }
            } else {
                if (this.broadcast.online) {
                    this.subscribe();
                }
            }

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
