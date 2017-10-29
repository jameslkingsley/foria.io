<template>
    <div class="main-page-columns grid grid-template-manager with-borders card">
        <div class="grid-item p-0">
            <div class="f-vertical-nav">
                <a :class="navItemClasses(t)" v-for="(t, i) in tabs" @click.prevent="selectTab(t)">
                    <span v-text="t.name"></span>
                    <p v-text="t.description"></p>
                </a>
            </div>
        </div>

        <div class="grid-item">
            <component
                :users="users"
                v-bind:is="contentComponent">
            </component>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['users'],

        data() {
            return {
                activeTab: opt({}),
                tabs: [
                    { id: 'revenue', name: 'Revenue', description: '', component: 'f-dashboard-revenue' },
                    { id: 'applications', name: 'Applications', description: '', component: 'f-dashboard-applications' }
                ]
            };
        },

        computed: {
            contentComponent() {
                return this.activeTab.component;
            }
        },

        methods: {
            navItemClasses(tab) {
                return {
                    'f-vertical-nav-item': true,
                    'is-active': tab.id === opt(this.activeTab).id
                };
            },

            selectTab(tab) {
                this.activeTab = tab;
            }
        },

        created() {
            //
        }
    }
</script>
