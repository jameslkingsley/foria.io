<template>
    <div>
        <div class="profile-sidebar">
            <f-profile-avatar :user="user"></f-profile-avatar>

            <h1 class="profile-name">
                {{ user.name }}
            </h1>

            <p class="profile-bio">{{ user.bio }}</p>

            <div class="profile-meta">
                <div class="profile-meta-row">
                    <i class="material-icons">date_range</i>
                    <span>Joined {{ user.created_at | calendar }}</span>
                </div>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-main-nav">
                <a :class="tabClasses(index)" v-for="(tab, index) in tabs" v-text="tab.title" @click.prevent="changeTab(index)"></a>
                <f-subscribe v-if="! user.is_mine" :user="user" class="is-pulled-right" style="margin-top:12px"></f-subscribe>
            </div>

            <div class="profile-main-content">
                <component
                    :user="user"
                    v-bind:is="contentComponent">
                </component>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                tabIndex: 0,
                tabs: [
                    { title: 'Videos', component: 'f-video-list' },
                    { title: 'Broadcasts', component: '' }
                ]
            };
        },

        computed: {
            contentComponent() {
                return this.activeTab.component;
            },

            activeTab() {
                return this.tabs[this.tabIndex];
            }
        },

        methods: {
            changeTab(index) {
                this.tabIndex = index;
            },

            tabClasses(index) {
                return {
                    'profile-main-nav-item': true,
                    'is-active': index === this.tabIndex
                };
            }
        }
    }
</script>
