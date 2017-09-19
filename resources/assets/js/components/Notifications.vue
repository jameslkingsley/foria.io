<template>
    <b-dropdown position="is-bottom-left" id="notifications">
        <a class="navbar-item" slot="trigger">
            <i class="material-icons">notifications</i>
            <span v-show="count" class="m-l-1">{{ count }}</span>
        </a>

        <b-dropdown-item custom>
            <button v-if="! empty" @click.prevent="markAsRead" class="button is-pulled-right m-r-3 m-b-3">Mark all as read</button>

            <div v-if="loaded && ! empty" class="notification-list">
                <a :href="url(item)" v-for="item in alerts" class="notification-item">
                    <span class="notification-item-icon">
                        <i class="material-icons">{{ item.data.icon }}</i>
                    </span>

                    <span class="notification-item-text" v-html="item.data.text"></span>

                    <span class="notification-item-timestamp">
                        {{ item.data.timestamp.date | fromnow }}
                    </span>
                </a>
            </div>

            <div v-if="empty" class="p-3 has-text-centered notification-list">
                All caught up!
            </div>

            <span v-if="! loaded">Loading...</span>
        </b-dropdown-item>
    </b-dropdown>
</template>

<script>
    export default {
        props: {
            items: {
                type: Array,
                default: null
            }
        },

        data() {
            return {
                alerts: this.items
            };
        },

        computed: {
            loaded() {
                return this.alerts !== null;
            },

            empty() {
                return this.alerts.length === 0;
            },

            count() {
                return this.alerts.length;
            }
        },

        methods: {
            url(item) {
                return item.data.url.length ? item.data.url : 'javascript:void(0)';
            },

            markAsRead() {
                return ajax.delete('/api/notifications/clear')
                    .then(r => this.alerts = []);
            }
        },

        created() {
            Echo.private(`App.Models.User.${Foria.user.id}`)
                .notification(n => {
                    this.alerts.unshift({ data: n });
                });
        }
    }
</script>
