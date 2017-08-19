<template>
    <div v-if="user">
        <div :class="addCardModalClasses">
            <div class="modal-background" @click="isAddingCard = false"></div>

            <div class="modal-content card p-4">
                <f-form url="/settings/billing" :submit="submitCard" confirm="Save Card" id="billing-form">
                    <span class="settings-title is-pulled-left w100 m-b-3" for="billing-card-element">
                        Credit or debit card
                    </span>

                    <div class="is-pulled-left w100" id="billing-card-element"></div>
                    <div class="is-pulled-left w100" id="billing-card-errors" role="alert"></div>

                    <!-- <button class="button is-pulled-right m-t-3" @click.prevent="cancelAddCard">Cancel</button> -->
                </f-form>
            </div>

            <button class="modal-close is-large" aria-label="close" @click="isAddingCard = false"></button>
        </div>

        <h3 class="settings-title">
            Your Card
            <button
                class="button is-pulled-right is-primary"
                @click="addCard"
                :disabled="user.has_card_on_file"
                v-show="! user.has_card_on_file">
                Add Card
            </button>
        </h3>

        <p v-show="! user.has_card_on_file">You haven't added a card.</p>

        <div v-show="user.has_card_on_file" class="is-pulled-left w100 box billing-card p-3 m-t-3 m-b-0">
            {{ user.card_brand }} <span v-html="formatLastFour(user.card_last_four)"></span>
            <button class="button is-pulled-right is-small" @click="removeCard" :disabled="isRemovingCard">Remove</button>
        </div>

        <hr />

        <h3 class="settings-title">
            Your Purchases
        </h3>

        <p>You haven't made any purchases.</p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: {},
                stripe: {},
                isAddingCard: false,
                isRemovingCard: false
            };
        },

        computed: {
            addCardModalClasses() {
                return {
                    'modal': true,
                    'is-active': this.isAddingCard
                };
            }
        },

        methods: {
            formatLastFour(value) {
                return Util.formatLastFour(value);
            },

            removeCard() {
                this.isRemovingCard = true;

                axios.delete(`/settings/billing/1`).then(r => {
                    this.getBillingInfo();
                    this.isRemovingCard = false;

                    this.$toast.open({
                        message: 'Card Removed',
                        type: 'is-success'
                    });
                });
            },

            getBillingInfo() {
                axios.get('/settings/billing').then(({ data }) => this.user = data);
            },

            addCard() {
                this.isAddingCard = true;
            },

            cancelAddCard() {
                this.isAddingCard = false;
            },

            submitCard() {
                this.stripe.stripe.createToken(this.stripe.card).then(result => {
                    if (result.error) {
                        var errorElement = document.getElementById('billing-card-errors');
                        errorElement.textContent = result.error.message;
                    } else {
                        const form = document.getElementById('billing-form');
                        const hiddenInput = document.createElement('input');

                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'stripeToken');
                        hiddenInput.setAttribute('value', result.token.id);
                        form.appendChild(hiddenInput);

                        axios.post('/settings/billing', formToObject(form)).then(r => {
                            this.$toast.open({
                                message: 'Card Added',
                                type: 'is-success'
                            });

                            this.getBillingInfo();
                            this.stripe.card.clear();
                            this.isAddingCard = false;
                        }).catch(({ response }) => {
                            this.isAddingCard = false;

                            this.$toast.open({
                                message: response.data.message,
                                type: 'is-danger'
                            });
                        });
                    }
                });
            }
        },

        created() {
            this.getBillingInfo();
        },

        mounted() {
            this.stripe.stripe = Stripe(Foria.stripeKey);
            this.stripe.elements = this.stripe.stripe.elements();
            this.stripe.card = this.stripe.elements.create('card');
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
