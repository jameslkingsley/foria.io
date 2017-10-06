<template>
    <div>
        <span class="button-tag" v-if="loaded && tag && ! subscribed" v-text="tag"></span>

        <b-dropdown position="is-bottom-left" :id="containerId">
            <a :class="triggerClasses" slot="trigger">
                <span>{{ text }}</span>
                <i class="material-icons m-l-1">keyboard_arrow_down</i>
            </a>

            <b-dropdown-item custom v-if="loaded">
                <div v-if="!auth || unauthorized" class="subscription-content has-text-centered">
                    <p>You need to be logged in to subscribe. Click the buttons below to login or register.</p>
                    <a href="/login" class="button is-primary m-t-3 m-b-3">Login</a>
                    <a href="/register" class="button is-primary m-t-3 m-b-3">Register</a>
                </div>

                <div v-if="auth && auth.has_card_on_file">
                    <div class="subscription-content has-text-centered" v-if="subscribed">
                        <small class="is-muted">
                            {{ willCancel ? 'Expires' : 'Renews' }} on {{ subscription.ends_at | datetime }}
                        </small>

                        <span class="subtitle">
                            Your subscription {{ willCancel ? 'expires' : 'renews' }} {{ subscription.ends_at | todate }}
                        </span>

                        <button v-if="! willCancel" :class="cancelClasses" @click="cancel">Cancel Subscription</button>
                    </div>

                    <div class="token-checkout" v-else>
                        <div v-for="(plan, index) in plans" :class="planClasses(plan)">
                            <span class="token-package-title">{{ plan.name }}</span>

                            <div class="token-package-controls">
                                <div class="token-package-controls-group">
                                    <span class="token-package-cost">{{ plan.amount | currency }} / {{ plan.interval | capitalize }}</span>
                                    <button :disabled="plan.disabled" class="button token-package-button" @click.prevent="selectPlan(plan)">Choose</button>
                                </div>
                            </div>
                        </div>

                        <div class="block m-t-3">
                            <span class="token-checkout-subtitle">
                                Using card on file
                                <a href="/settings/#billing" class="is-pulled-right">Change</a>
                            </span>

                            <span class="token-checkout-card-brand">{{ auth.card_brand }}</span>
                            <span class="token-checkout-card-number" v-html="formatLastFour(auth.card_last_four)"></span>

                            <div class="block m-t-3">
                                <div class="columns">
                                    <div class="column">
                                        <small>
                                            All purchases are final and cannot be refunded. <a>Terms &amp; Conditions</a>.
                                        </small>
                                    </div>

                                    <div class="column">
                                        <button :class="createClasses" :disabled="createButtonDisabled" @click="create">
                                            Subscribe for {{ amount | currency }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="auth && !auth.has_card_on_file && !unauthorized" class="subscription-content has-text-centered">
                    <p>Before you can subscribe, you need to add a card to your account. Click the button below.</p>
                    <a href="/settings/#billing" class="button is-primary m-t-3 m-b-3">Add a card</a>
                </div>
            </b-dropdown-item>
        </b-dropdown>
    </div>
</template>

<script>
    export default {
        props: {
            user: { type: Object },
            plan: { default: null },
            tag: { type: String, default: '' }
        },

        data() {
            return {
                auth: null,
                loaded: false,
                subscription: null,
                unauthorized: false,
                isCancelling: false,
                isCreating: false,
                planId: this.plan || 'bronze',
                plans: [],
                planMap: {
                    bronze: 1,
                    silver: 2,
                    gold: 3
                }
            };
        },

        computed: {
            containerId() {
                if (! this.loaded || this.unauthorized) {
                    return '';
                }

                return (this.auth.has_card_on_file && ! this.subscribed)
                    ? 'subscription-checkout'
                    : '';
            },

            icon() {
                return this.subscribed ? 'close' : 'check';
            },

            text() {
                return this.subscribed ? 'Subscription' : 'Subscribe';
            },

            subscribed() {
                return this.subscription !== null;
            },

            willCancel() {
                return this.subscription.cancels_at !== null;
            },

            cancelClasses() {
                return {
                    'button': true,
                    'is-loading': this.isCancelling
                };
            },

            createClasses() {
                return {
                    'button': true,
                    'is-primary': true,
                    'is-pulled-right': true,
                    'is-loading': this.isCreating
                };
            },

            triggerClasses() {
                return {
                    'button': true,
                    'is-primary': true,
                    'is-loading': ! this.loaded
                };
            },

            amount() {
                return (! this.plans.length) ? 0 : this.plans.find(
                    p => p.id == this.planId
                ).amount;
            },

            createButtonDisabled() {
                return this.isCreating;
            }
        },

        methods: {
            formatLastFour(value) {
                return Util.formatLastFour(value);
            },

            planClasses(plan) {
                return {
                    'token-package': true,
                    'is-selected': this.planId == plan.id,
                    'is-disabled': plan.disabled
                };
            },

            selectPlan(plan) {
                if (plan.disabled) return;

                this.planId = plan.id;
            },

            create() {
                this.isCreating = true;

                ajax.post('/api/subscription', { user_id: this.user.id, plan: this.planId }).then(r => {
                    this.fetch();
                    this.isCreating = false;
                    this.$emit('success');
                });
            },

            cancel() {
                this.isCancelling = true;

                ajax.delete(`/api/subscription/${this.user.name}`).then(r => {
                    this.fetch();
                    this.isCancelling = false;
                });
            },

            fetch() {
                ajax.get(`/api/subscription/${this.user.name}`)
                    .then(r => {
                        this.subscription = r.data.subscription;
                        this.auth = r.data.user;
                        this.plans = r.data.plans;
                        this.loaded = true;

                        if (this.plan) {
                            this.plans = _.map(this.plans, plan => {
                                plan.disabled = this.planMap[plan.id] < this.planMap[this.plan];
                                return plan;
                            });
                        }
                    })
                    .catch(e => {
                        this.loaded = true;

                        if (e.response.status == 401) {
                            this.unauthorized = true;
                        }
                    });
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
