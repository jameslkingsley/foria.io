<template>
    <div class="token-checkout">
        <h1 class="title has-text-centered m-b-5">You have {{ user.tokens }} tokens</h1>

        <form method="post" id="payment-form" action="/tokens">
            <input type="hidden" name="package_id" v-model="packageId">

            <div class="columns">
                <div class="column is-8">
                    <div v-for="(package, index) in packages" :key="index" :class="packageClasses(package)">
                        <span class="token-package-title">{{ package.token_count }} Tokens</span>
                        <div class="token-package-controls">
                            <div class="token-package-controls-group">
                                <span class="token-package-cost">{{ package.cost | currency }}</span>
                                <button class="button token-package-button" @click.prevent="selectPackage(package)">Choose</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="column is-4">
                    <label for="card-element">
                        Credit or debit card
                    </label>

                    <div id="card-element">
                        <!-- Stripe element will be inserted here. -->
                    </div>

                    <!-- Used to display element errors -->
                    <div id="card-errors" role="alert"></div>

                    <div class="block m-t-3">
                        <div class="columns">
                            <div class="column">
                                <small>
                                    All purchases are final and cannot be refunded.
                                </small>
                            </div>

                            <div class="column">
                                <button class="button is-primary is-pulled-right">Agree &amp; Pay</button>
                            </div>
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
                packages: []
            };
        },

        computed: {
            //
        },

        methods: {
            selectPackage(p) {
                this.packageId = p.id;
            },

            packageClasses(p) {
                return {
                    'token-package': true,
                    'is-selected': this.packageId == p.id
                };
            }
        },

        created() {
            axios.get('/tokens/packages').then(r => this.packages = r.data);
        },

        mounted() {
            const stripe = Stripe('pk_test_jg2tIvZROxeScjJ5sitk5RaH');
            const elements = stripe.elements();

            // Create an instance of the card element
            const card = elements.create('card');

            // Add an instance of the card Element into the `card-element` <div>
            card.mount('#card-element');

            card.addEventListener('change', ({error}) => {
                const displayError = document.getElementById('card-errors');

                if (error) {
                    displayError.textContent = error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                stripe.createToken(card).then(function(result) {
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

                        // Submit the form
                        form.submit();
                    }
                });
            });
        }
    }
</script>
