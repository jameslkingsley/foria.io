<template>
    <div v-if="loaded">
        <h3 class="settings-title">Subscriptions</h3>

        <table class="table" v-show="subscriptions.length">
            <tr>
                <th>Model</th>
                <th>Tier</th>
                <th>Renewals</th>
                <th>Started On</th>
                <th>Renews On</th>
                <th>Expires On</th>
            </tr>

            <tr v-for="sub in subscriptions">
                <td>{{ sub.to.name }}</td>
                <td>{{ sub.stripe_plan | capitalize }}</td>
                <td>{{ sub.renewals }}</td>
                <td>{{ sub.created_at | datetime }}</td>

                <td v-if="sub.cancels_at">
                    N/A
                </td>

                <td v-else>
                    {{ sub.ends_at | datetime }}
                </td>

                <td v-if="sub.cancels_at">
                    {{ sub.cancels_at | datetime }}
                </td>

                <td v-else>
                    N/A
                </td>
            </tr>
        </table>

        <p v-show="! subscriptions.length">You haven't subscribed to any models.</p>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                loaded: false,
                subscriptions: []
            };
        },

        methods: {
            fetch() {
                axios.get('/api/subscription').then(r => {
                    this.subscriptions = r.data;
                    this.loaded = true;
                });
            }
        },

        mounted() {
            this.fetch();
        }
    }
</script>
