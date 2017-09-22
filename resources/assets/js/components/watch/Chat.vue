<template>
    <div class="chat">
        <div class="chat-list" id="f-chat-list">
            <div :class="messageClasses(message)" v-for="(message, index) in sortedMessages">
                <div v-if="message.is_alert">
                    <span class="chat-item-alert">{{ message.text }}</span>
                </div>

                <div v-else>
                    <span class="chat-item-author">{{ message.sender.name }}</span>
                    <span class="chat-item-text">{{ message.text }}</span>
                </div>
            </div>
        </div>

        <div class="chat-form">
            <form method="post" @submit.prevent="send">
                <input class="input" name="text" v-model="form.text" placeholder="Type a message" :disabled="! fetchCompleted">
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user', 'broadcast'],

        data() {
            return {
                messages: [],
                fetchCompleted: false,
                form: {
                    text: '',
                    receiver: this.user,
                    broadcast: this.broadcast
                }
            };
        },

        computed: {
            sortedMessages() {
                return _.sortBy(this.messages, ['created_at']);
            }
        },

        methods: {
            messageClasses(message) {
                return {
                    'chat-item': true,
                    'is-alert': message.is_alert,
                    'is-subscription': message.options.is_subscription,
                    'is-token': message.options.is_token
                };
            },

            send() {
                axios.post('/api/chat', this.form).then(r => {
                    this.form.text = '';
                });
            },

            fetch() {
                axios.get(`/api/chat/past/${this.user.name}`).then(r => {
                    this.messages = r.data;
                    this.fetchCompleted = true;
                });
            }
        },

        updated() {
            let list = document.getElementById('f-chat-list');
            list.scrollTop = list.scrollHeight;
        },

        mounted() {
            this.fetch();

            Echo.channel(`watch-${this.user.id}`)
                .listen('ChatMessageSent', e => {
                    this.messages.push(e.message);
                });
        }
    }
</script>
