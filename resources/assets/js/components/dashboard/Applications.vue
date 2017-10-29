<template>
    <div>
        <table class="table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Full Name</th>
                    <th>Maiden Name</th>
                    <th>Nicknames</th>
                    <th>Age</th>
                    <th>Country</th>
                    <th>Photo ID</th>
                    <th>Photo Self</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(app, index) in applications">
                    <td>{{ app.updated_at | date }}</td>
                    <td v-text="app.user.name"></td>
                    <td v-text="app.full_name"></td>
                    <td v-text="app.maiden_name"></td>
                    <td v-text="app.nicknames"></td>
                    <td>{{ app.date_of_birth | age }}</td>
                    <td v-text="app.country"></td>

                    <td>
                        <figure class="image is-64x64">
                            <img :src="app.photo_id">
                        </figure>
                    </td>

                    <td>
                        <figure class="image is-64x64">
                            <img :src="app.photo_self">
                        </figure>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: ['users'],

        data() {
            return {
                applications: []
            };
        },

        methods: {
            fetch() {
                ajax.get('/api/dashboard/applications')
                    .then(r => this.applications = r.data.applications);
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
