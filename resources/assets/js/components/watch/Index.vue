<template>
    <div class="columns">
        <div class="column is-9">
            <div class="m-b-3">
                <div class="watch-header">
                    <div class="watch-title">
                        <f-watch-topic :text="topic" :editable="user.is_mine"></f-watch-topic>
                        <a :href="user.profile_url" class="watch-user">{{ user.name }}</a>
                    </div>

                    <div class="watch-controls">
                        <f-subscribe v-if="! user.is_mine" class="is-pulled-right m-l-2" :user="user"></f-subscribe>
                        <f-follow v-if="! user.is_mine" class="is-pulled-right" :user="user"></f-follow>

                        <b-dropdown v-if="user.is_mine" class="is-pulled-right" position="is-bottom-left">
                            <button class="button" slot="trigger">
                                <i class="material-icons">settings</i>
                            </button>

                            <b-dropdown-item>Subscriber mode</b-dropdown-item>
                        </b-dropdown>

                        <button v-if="user.is_mine" :class="startStopClasses" :disabled="! online && startingStream" @click="startOrStop">
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
                        controls
                        autoplay
                        ref="video"
                        width="100%"
                        height="540"
                        id="watch-video-driver"
                        v-if="online && !online"
                        :class="videoClasses"
                        :poster="user.avatar_url"
                        :data-setup="videoSetupJson">
                    </video>

                    <video
                        controls
                        autoplay
                        ref="video"
                        width="100%"
                        height="540"
                        id="watch-video-driver"
                        class="is-pulled-left"
                        :poster="user.avatar_url">
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
                startingStream: false,
                topic: this.broadcast.topic || 'My First Stream!',
                online: this.broadcast.online || false,
                videoSetup: {
                    fluid: true
                }
            };
        },

        computed: {
            hasBroadcast() {
                return typeof this.broadcast === 'object';
            },

            videoClasses() {
                return {
                    'video-js': true,
                    'vjs-default-skin': true,
                    'vjs-big-play-centered': true,
                    'vjs-16-9': true
                };
            },

            startStopClasses() {
                return {
                    'm-r-2': true,
                    'button': true,
                    'has-icon': true,
                    'is-primary': true,
                    'is-pulled-right': true,
                    'is-loading': this.startingStream && !this.online
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
                console.log('Watch: Publishing');
                var vm = this;

                this.startingStream = true;

                this.stream = LiveStream.Publisher({
                    driver: {
                        streamName: this.user.name,
                        mediaElementId: 'watch-video-driver'
                    },

                    createNew: ! this.online,

                    onStart() {
                        vm.$refs.video.load();
                        vm.$refs.video.play();
                    },

                    onBroadcasting(broadcast) {
                        vm.online = broadcast.online;
                        vm.startingStream = false;
                    },

                    onFail() {
                        vm.offline = {
                            title: 'Error',
                            icon: 'error_outline',
                            text: 'Failed to start live stream.'
                        };
                    }
                });
            },

            subscribe() {
                console.log('Watch: Subscribing');
                LiveStream.Subscriber({
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

            Echo.channel(`watch-${this.user.name}`)
                .listen('TopicChanged', e => {
                    this.topic = e.topic;
                })
                .listen('NewSubscription', e => {
                    //
                });
        }
    }
</script>
