<template>
    <b-autocomplete
        v-model="name"
        :field="field"
        :data="filteredUsers"
        :placeholder="placeholder"
        :icon="icon"
        @select="selectUser"
        @input="inputUser">
        <template slot="empty">{{ notFoundText }}</template>
    </b-autocomplete>
</template>

<script>
    export default {
        props: {
            users: { type: Array, default: () => {return []} },
            field: { type: String, default: 'name' },
            icon: { type: String, default: 'search' },
            placeholder: { type: String, default: 'Filter by user' },
            notFoundText: { type: String, default: 'No results found' }
        },

        data() {
            return {
                name: ''
            };
        },

        computed: {
            filteredUsers() {
                return this.users.filter(user => {
                    return user.name
                        .toLowerCase()
                        .indexOf(this.name.toLowerCase()) >= 0
                });
            }
        },

        methods: {
            selectUser(user) {
                if (user === null) return;
                this.$emit('select', user);
            },

            inputUser() {
                if (!this.name.length) {
                    this.$emit('clear');
                }
            }
        }
    }
</script>
