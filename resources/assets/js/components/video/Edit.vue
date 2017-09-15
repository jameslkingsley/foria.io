<template>
    <div class="card p-3">
        <h3 class="settings-title m-b-3">
            Edit Video
        </h3>

        <f-form confirm="Save Changes &amp; Publish" @submit="submit">
            <b-field>
                <b-input name="name" :value="video.name"></b-input>
            </b-field>

            <b-field>
                <p>How do you want people to access this video?</p>

                <div class="video-edit-access-option">
                    <input class="veao-field" type="number" v-model="tokenPrice">
                    <span class="veao-label">Tokens</span>
                    <small class="veao-subtext"></small>
                </div>

                <p class="veao-separator">OR</p>

                <div class="video-edit-access-option">
                    <div class="veao-plan" v-for="plan in plans" @click.prevent="choosePlan(plan)">
                        <span class="veaop-title">{{ plan.title }}</span>
                        <small class="veaop-price">{{ plan.price }}</small>
                    </div>
                </div>
            </b-field>
        </f-form>
    </div>
</template>

<script>
    export default {
        props: ['video'],

        data() {
            return {
                accessOption: 'tokens',
                selectedPlan: null,
                tokenPrice: 0,
                plans: [
                    { id: 'bronze', title: 'Bronze', price: '£4.99/month' },
                    { id: 'silver', title: 'Silver', price: '£9.99/month' },
                    { id: 'gold', title: 'Gold', price: '£24.99/month' }
                ]
            };
        },

        methods: {
            submit(data) {
                ajax.post(`/api/videos/${this.video.id}`, data)
                    .then(r => {
                        this.$toast.open({
                            message: 'Changes Saved',
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            choosePlan(plan) {
                this.selectedPlan = plan.id;
            }
        }
    }
</script>
