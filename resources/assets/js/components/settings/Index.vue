<template>
    <div class="columns">
        <div class="column is-sidebar settings-nav">
            <a
                :class="navItemClasses(index)"
                v-for="(panel, index) in panels"
                @click.prevent="selectPanel(index)">
                <i class="material-icons">{{ panel.icon }}</i>
                {{ panel.name }}
            </a>
        </div>

        <div class="column settings-content card">
            <component
                :user="user"
                v-bind:is="contentComponent">
            </component>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                activePanel: 1,
                panels: [
                    { name: 'Account', icon: 'account_circle', component: 'f-settings-account' },
                    { name: 'Billing', icon: 'credit_card', component: 'f-settings-billing' },
                    { name: 'Subscriptions', icon: 'subscriptions', component: 'f-settings-subscriptions' },
                    { name: 'Notifications', icon: 'notifications', component: 'f-settings-notifications' }
                ]
            };
        },

        computed: {
            contentComponent() {
                let panel = this.panels[this.activePanel];
                return panel.component;
            }
        },

        methods: {
            navItemClasses(index) {
                return {
                    'settings-nav-item': true,
                    'is-active': index === this.activePanel
                };
            },

            selectPanel(index) {
                let panel = this.panels[index];
                this.activePanel = index;
            }
        }
    }
</script>
