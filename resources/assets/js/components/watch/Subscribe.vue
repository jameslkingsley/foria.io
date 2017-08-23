<template>
    <b-dropdown position="is-bottom-left" :id="containerId">
        <a :class="triggerClasses" slot="trigger">
            <span>{{ text }}</span>
            <i class="material-icons m-l-1">keyboard_arrow_down</i>
        </a>

        <b-dropdown-item custom v-if="loaded">
            <div v-if="auth.has_card_on_file">
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
                                <button class="button token-package-button" @click.prevent="selectPlan(plan)">Choose</button>
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

            <div v-else class="subscription-content has-text-centered">
                <p>Before you can subscribe, you need to add a card to your account. Click the button below.</p>
                <a href="/settings/#billing" class="button is-primary m-t-3 m-b-3">Add a card</a>
            </div>
        </b-dropdown-item>
    </b-dropdown>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                auth: null,
                loaded: false,
                subscription: null,
                isCancelling: false,
                isCreating: false,
                planId: 'bronze',
                plans: []
            };
        },

        computed: {
            containerId() {
                if (! this.loaded) {
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
            /**
             * Formats the last four card numbers.
             */
            formatLastFour(value) {
                return Util.formatLastFour(value);
            },

            /**
             * Gets the classes for the given plan.
             */
            planClasses(plan) {
                return {
                    'token-package': true,
                    'is-selected': this.planId == plan.id
                };
            },

            /**
             * Selects the given plan.
             */
            selectPlan(plan) {
                this.planId = plan.id;
            },

            /**
             * Creates the subscription.
             */
            create() {
                this.isCreating = true;

                axios.post('/api/subscription', { user_id: this.user.id, plan: this.planId }).then(r => {
                    this.fetch();
                    this.isCreating = false;
                });
            },

            /**
             * Cancels the subscription.
             */
            cancel() {
                this.isCancelling = true;

                axios.delete(`/api/subscription/${this.user.id}`).then(r => {
                    this.fetch();
                    this.isCancelling = false;
                });
            },

            /**
             * Gets the subscription data.
             */
            fetch() {
                axios.get(`/api/subscription/${this.user.id}`).then(r => {
                    this.subscription = r.data.subscription;
                    this.auth = r.data.user;
                    this.plans = r.data.plans;
                    this.loaded = true;
                });
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
