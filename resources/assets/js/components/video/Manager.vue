<template>
    <div class="main-page-columns grid grid-template-manager with-info with-borders card">
        <div class="grid-item p-0">
            <!-- <f-vertical-nav>
                <f-vertical-nav-item
                    v-for="(v, i) in videos"
                    @click="edit(v)"
                    :text="v.name"
                    :icon="v.thumbnail"
                    :subtext="v.created_at">
                </f-vertical-nav-item>
            </f-vertical-nav> -->
            <div class="f-vertical-nav">
                <a :class="navItemClasses(v)" v-for="(v, i) in videos" @click.prevent="edit(v)">
                    <i :style="'background-image: url('+v.thumbnail+')'"></i>
                    <span v-text="v.name"></span>
                    <p>{{ v.views | locale }} views</p>
                </a>
            </div>
        </div>

        <div class="grid-item">
            <f-form confirm="Save Changes" @submit="submit" :footer-style="footerStyle" :submit-style="submitStyle">
                <b-field label="Title">
                    <b-input name="name" v-model="video.name"></b-input>
                </b-field>

                <hr />

                <p class="has-text-centered subtitle m-0">Video Access</p>

                <div class="video-edit-access-option">
                    <p class="veao-description">
                        Require users to pay with tokens.
                        <br />
                        <strong v-show="video.token_price">You will receive {{ tokenPriceNet | currency }}</strong>
                    </p>

                    <b-input placeholder="Amount"
                        type="number"
                        v-model="video.token_price"
                        class="veao-field"
                        icon="local_play">
                    </b-input>
                </div>

                <p class="veao-separator">OR</p>

                <div class="video-edit-access-option">
                    <p class="veao-description">
                        Require users to be subscribed with the selected plan (or higher).

                        <br />

                        <strong v-show="video.required_subscription">
                            You will receive {{ selectedPlanNet | currency }} each month*
                        </strong>
                    </p>

                    <div v-for="plan in plans" :class="planClasses(plan)" @click.prevent="choosePlan(plan)">
                        <span class="veaop-title">{{ plan.title }}</span>
                        <span class="veaop-price">{{ plan.price | currency }}/month</span>
                    </div>
                </div>

                <hr />

                <b-switch slot="footer"
                    v-model="video.privacy"
                    true-value="public"
                    false-value="private"
                    class="is-pulled-left">
                    {{ video.privacy | capitalize }}
                </b-switch>
            </f-form>
        </div>

        <div class="grid-item">
            <h2 class="subtitle m-b-2">Tokens</h2>
            <p class="text-small m-b-3">You can set a token price for your video, which will require users to purchase tokens and use those tokens to gain access to your video. Upon purchase, the given token price will be added to your account.</p>

            <h2 class="subtitle m-b-2">Subscriptions</h2>
            <p class="text-small m-b-3">You can also choose a subscription plan that is required for users to access the video. A subscription can provide you with a regular income each month*, and offers the user added benefits such as access to your other videos that meet the required plan and access to subscriber only shows**.</p>

            <small>
                * Subscriptions will auto-renew for the user each month, however they have the option to cancel their subscription at anytime.
                <br /><br />
                ** In the near future you will be able to live stream your webcam to your audience, collect tips and host subscriber only shows.
            </small>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['videos'],

        data() {
            return {
                video: this.videos.length ? this.videos[0] : {},
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
                let pence = (this.video.token_price * 0.1) * 100;

                return 0.7 * pence;
            },

            selectedPlanNet() {
                let plan = _.find(this.plans, ['id', this.video.required_subscription]);

                if (plan) {
                    return plan.price * 0.6;
                }

                return 0;
            }
        },

        watch: {
            video: {
                token_price() {
                    if (this.video.token_price > 0) {
                        this.video.required_subscription = null;
                    }
                }
            }
        },

        methods: {
            navItemClasses(video) {
                return {
                    'f-vertical-nav-item': true,
                    'is-active': video.ref === opt(this.video).ref
                };
            },

            submit(data) {
                ajax.post(`/api/videos/${this.video.ref}`, this.video)
                    .then(r => {
                        this.$toast.open({
                            message: 'Changes Saved',
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            edit(video) {
                this.video = video;
            },

            choosePlan(plan) {
                this.video.required_subscription = plan.id;
                this.video.token_price = null;
            },

            planClasses(plan) {
                return {
                    'veao-plan': true,
                    'is-active': plan.id === this.video.required_subscription && this.video.token_price === null
                };
            }
        },

        created() {
            //
        }
    }
</script>
