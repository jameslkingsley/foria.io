<template>
    <div class="token-checkout">
        <form method="post" id="payment-form" action="/tokens" @submit.prevent="pay">
            <input type="hidden" name="package_id" v-model="packageId">
            <input type="hidden" name="_token" :value="csrfToken">

            <div v-for="(package, index) in packages" :key="index" :class="packageClasses(package)">
                <span class="token-package-title">{{ package.token_count }} Tokens</span>

                <div class="token-package-controls">
                    <div class="token-package-controls-group">
                        <span class="token-package-cost">{{ package.cost | currency }}</span>
                        <button class="button token-package-button" @click.prevent="selectPackage(package)">Choose</button>
                    </div>
                </div>
            </div>

            <div class="block m-t-3">
                <div v-if="! user.has_card_on_file">
                    <label for="card-element">
                        Credit or debit card
                    </label>

                    <div id="card-element">
                        <!-- Stripe element will be inserted here. -->
                    </div>
                </div>

                <div v-else>
                    <span class="token-checkout-subtitle">
                        Using card on file
                        <a href="/settings/#billing" class="is-pulled-right">Change</a>
                    </span>

                    <span class="token-checkout-card-brand">{{ user.card_brand }}</span>
                    <span class="token-checkout-card-number" v-html="formatLastFour(user.card_last_four)"></span>
                </div>

                <!-- Used to display element errors -->
                <div id="card-errors" role="alert"></div>

                <div class="block m-t-3">
                    <div class="columns">
                        <div class="column">
                            <small>
                                All purchases are final and cannot be refunded. <a>Terms &amp; Conditions</a>.
                            </small>
                        </div>

                        <div class="column">
                            <button class="button is-primary is-pulled-right" :disabled="buttonState">Agree &amp; Pay {{ cost | currency }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                packageId: 1,
                csrfToken: Foria.csrfToken,
                packages: [],
                stripe: {},
                buttonState: false
            };
        },

        computed: {
            cost() {
                return (! this.packages.length) ? 0 : this.packages.find(
                    p => p.id == this.packageId
                ).cost;
            }
        },

        methods: {
            formatLastFour(value) {
                return Util.formatLastFour(value);
            },

            selectPackage(p) {
                this.packageId = p.id;
            },

            packageClasses(p) {
                return {
                    'token-package': true,
                    'is-selected': this.packageId == p.id
                };
            },

            pay() {
                this.buttonState = true;

                if (this.user.has_card_on_file) {
                    const form = document.getElementById('payment-form');

                    axios.post('/tokens', formToObject(form)).then(r => {
                        this.$toast.open({
                            message: r.data.message,
                            type: `is-${r.data.style}`
                        });

                        this.buttonState = false;
                    });
                } else {
                    this.stripe.stripe.createToken(this.stripe.card).then(result => {
                        if (result.error) {
                            // Inform the user if there was an error
                            var errorElement = document.getElementById('card-errors');
                            errorElement.textContent = result.error.message;
                        } else {
                            // Send the token to your server
                            // Insert the token ID into the form so it gets submitted to the server
                            const form = document.getElementById('payment-form');
                            const hiddenInput = document.createElement('input');

                            hiddenInput.setAttribute('type', 'hidden');
                            hiddenInput.setAttribute('name', 'stripeToken');
                            hiddenInput.setAttribute('value', result.token.id);
                            form.appendChild(hiddenInput);

                            axios.post('/tokens', formToObject(form)).then(r => {
                                console.log(r);

                                this.$toast.open({
                                    message: r.data.message,
                                    type: `is-${r.data.style}`
                                });

                                this.buttonState = false;

                                this.stripe.card.clear();
                            });
                        }
                    });
                }
            }
        },

        created() {
            axios.get('/tokens/packages').then(
                r => this.packages = r.data
            );
        },

        mounted() {
            if (! this.user.has_card_on_file) {
                this.stripe.stripe = Stripe(Foria.stripeKey);
                this.stripe.elements = this.stripe.stripe.elements();
                this.stripe.card = this.stripe.elements.create('card');
                this.stripe.card.mount('#card-element');

                this.stripe.card.addEventListener('change', ({error}) => {
                    const displayError = document.getElementById('card-errors');

                    if (error) {
                        displayError.textContent = error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            }
        }
    }
</script>
