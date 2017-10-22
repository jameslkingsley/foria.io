<template>
    <div class="card p-3">
        <h3 class="settings-title m-b-3">
            Edit Video
        </h3>

        <div class="columns">
            <div class="column is-4 is-offset-4">
                <f-form confirm="Save Changes" @submit="submit" :footer-style="footerStyle" :submit-style="submitStyle">
                    <b-field label="Title">
                        <b-input name="name" v-model="title"></b-input>
                    </b-field>

                    <hr />

                    <p class="has-text-centered subtitle m-0">Video Access</p>

                    <div class="video-edit-access-option">
                        <p class="veao-description">
                            Require users to pay with tokens.
                            <br />
                            <strong v-show="tokenPrice">You will receive {{ tokenPriceNet | currency }}</strong>
                        </p>

                        <b-input placeholder="Amount"
                            type="number"
                            v-model="tokenPrice"
                            class="veao-field"
                            icon="local_play">
                        </b-input>
                    </div>

                    <p class="veao-separator">OR</p>

                    <div class="video-edit-access-option">
                        <p class="veao-description">
                            Require users to be subscribed with the selected plan (or higher). You're guaranteed the first month's subscription.

                            <br /><br />

                            <strong v-show="selectedPlan">
                                You will receive {{ selectedPlanNet | currency }} each month (providing the user continues the subscription after the first month)
                            </strong>
                        </p>

                        <div v-for="plan in plans" :class="planClasses(plan)" @click.prevent="choosePlan(plan)">
                            <span class="veaop-title">{{ plan.title }}</span>
                            <span class="veaop-price">{{ plan.price | currency }}/month</span>
                        </div>
                    </div>

                    <hr />

                    <b-switch slot="footer"
                        v-model="privacy"
                        true-value="public"
                        false-value="private"
                        class="is-pulled-left">
                        {{ privacy | capitalize }}
                    </b-switch>
                </f-form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['video'],

        data() {
            return {
                accessOption: 'tokens',
                selectedPlan: this.video.required_subscription,
                tokenPrice: this.video.token_price,
                privacy: this.video.privacy,
                title: this.video.name,
                url: this.video.url,
                plans: [
                    { id: 'bronze', title: 'Bronze', price: 499 },
                    { id: 'silver', title: 'Silver', price: 999 },
                    { id: 'gold', title: 'Gold', price: 2499 }
                ],
                footerStyle: {
                    'float': 'left',
                    'width': '100%',
                    'display': 'block',
                    'margin-top': '2rem'
                },
                submitStyle: {
                    'margin-top': '0 !important'
                }
            };
        },

        computed: {
            tokenPriceNet() {
                let pence = (this.tokenPrice * 0.1) * 100;

                return 0.7 * pence;
            },

            selectedPlanNet() {
                let plan = _.find(this.plans, ['id', this.selectedPlan]);

                if (plan) {
                    return plan.price * 0.6;
                }

                return 0;
            }
        },

        watch: {
            tokenPrice() {
                if (this.tokenPrice > 0) {
                    this.selectedPlan = null;
                }
            }
        },

        methods: {
            submit(data) {
                ajax.post(`/api/videos/${this.video.ref}`, {
                    name: this.title,
                    privacy: this.privacy.toLowerCase(),
                    required_subscription: this.selectedPlan,
                    token_price: this.tokenPrice
                }).then(r => {
                    this.$toast.open({
                        message: 'Changes Saved',
                        type: 'is-success',
                        duration: 4000
                    });
                });
            },

            choosePlan(plan) {
                this.selectedPlan = plan.id;
                this.tokenPrice = null;
            },

            planClasses(plan) {
                return {
                    'veao-plan': true,
                    'is-active': plan.id === this.selectedPlan && this.tokenPrice === null
                };
            }
        }
    }
</script>
