<template>
    <div>
        <form method="post" id="billing-form" action="/settings/billing" @submit.prevent="save">
            <input type="hidden" name="_token" :value="csrfToken">

            <label for="billing-card-element">
                Credit or debit card
            </label>

            <div id="billing-card-element">
                <!-- Stripe element will be inserted here. -->
            </div>

            <!-- Used to display element errors -->
            <div id="billing-card-errors" role="alert"></div>

            <button class="button is-primary is-pulled-left">Save</button>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                csrfToken: Foria.csrfToken,
                stripe: {}
            };
        },

        methods: {
            save() {
                this.stripe.stripe.createToken(this.stripe.card).then(result => {
                    if (result.error) {
                        // Inform the user if there was an error
                        var errorElement = document.getElementById('billing-card-errors');
                        errorElement.textContent = result.error.message;
                    } else {
                        // Send the token to your server
                        // Insert the token ID into the form so it gets submitted to the server
                        const form = document.getElementById('billing-form');
                        const hiddenInput = document.createElement('input');

                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'stripeToken');
                        hiddenInput.setAttribute('value', result.token.id);
                        form.appendChild(hiddenInput);

                        axios.post('/settings/billing', formToObject(form)).then(r => {
                            console.log(r);

                            this.$toast.open({
                                message: 'Billing Details Saved',
                                type: 'is-success'
                            });

                            // this.stripe.card.clear();
                        });
                    }
                });
            }
        },

        mounted() {
            this.stripe.stripe = Stripe(Foria.stripeKey);
            this.stripe.elements = this.stripe.stripe.elements();

            // Create an instance of the card element
            this.stripe.card = this.stripe.elements.create('card');

            // Add an instance of the card Element into the `card-element` <div>
            this.stripe.card.mount('#billing-card-element');

            this.stripe.card.addEventListener('change', ({error}) => {
                const displayError = document.getElementById('billing-card-errors');

                if (error) {
                    displayError.textContent = error.message;
                } else {
                    displayError.textContent = '';
                }
            });
        }
    }
</script>
