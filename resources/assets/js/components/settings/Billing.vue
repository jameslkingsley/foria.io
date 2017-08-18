<template>
    <div>
        <div :class="addCardModalClasses">
            <div class="modal-background" @click="isAddingCard = false"></div>

            <div class="modal-content card p-4">
                <form method="post" id="billing-form" action="/settings/billing" @submit.prevent="saveCard">
                    <input type="hidden" name="_token" :value="csrfToken">

                    <span class="settings-title is-pulled-left w100 m-b-3" for="billing-card-element">
                        Credit or debit card
                    </span>

                    <div class="is-pulled-left w100" id="billing-card-element">
                        <!-- Stripe element will be inserted here. -->
                    </div>

                    <!-- Used to display element errors -->
                    <div class="is-pulled-left w100" id="billing-card-errors" role="alert"></div>

                    <button type="submit" class="button is-primary is-pulled-right m-t-3 m-l-2" :disabled="isSavingCard">Save Card</button>
                    <button class="button is-pulled-right m-t-3" @click.prevent="cancelAddCard">Cancel</button>
                </form>
            </div>

            <button class="modal-close is-large" aria-label="close" @click="isAddingCard = false"></button>
        </div>

        <h3 class="settings-title">
            Your Cards
            <button class="button is-pulled-right is-primary" @click="addCard">Add Card</button>
        </h3>

        <p v-show="isFetchingCards">Retreiving cards...</p>

        <table v-show="! isFetchingCards" class="table">
            <thead>
                <tr>
                    <th align="left">Brand</th>
                    <th align="left">Number</th>
                    <th align="left">Expiry</th>
                    <th align="right">&nbsp;</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(card, index) in cards">
                    <td align="left">{{ card.brand }}</td>
                    <td align="left">&#9913;&#9913;&#9913;&#9913; &#9913;&#9913;&#9913;&#9913; &#9913;&#9913;&#9913;&#9913; {{ card.last4 }}</td>
                    <td align="left">{{ card.exp_month }} / {{ card.exp_year }}</td>
                    <td align="right" style="padding-right: 0 !important"><button class="button is-pulled-right" @click="removeCard(card, index)" :disabled="isRemovingCard">Remove Card</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                csrfToken: Foria.csrfToken,
                stripe: {},
                cards: [],
                isAddingCard: false,
                isRemovingCard: false,
                isFetchingCards: false,
                isSavingCard: false
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
            removeCard(card, index) {
                this.isRemovingCard = true;

                axios.delete(`/settings/billing/${card.id}`).then(r => {
                    this.cards.splice(index, 1);
                    this.isRemovingCard = false;

                    this.$toast.open({
                        message: 'Card Removed',
                        type: 'is-success'
                    });
                });
            },

            addCard() {
                this.isAddingCard = true;
            },

            cancelAddCard() {
                this.isAddingCard = false;
                this.stripe.card.clear();
            },

            fetchCards() {
                this.isFetchingCards = true;

                axios.get('/settings/billing').then(r => {
                    this.cards = r.data.data;
                    this.isFetchingCards = false;
                });
            },

            saveCard() {
                this.isSavingCard = true;

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
                            console.log(r);

                            this.$toast.open({
                                message: 'Card Added',
                                type: 'is-success'
                            });

                            this.stripe.card.clear();
                            this.isAddingCard = false;
                            this.isSavingCard = false;

                            this.fetchCards();
                        });
                    }
                });
            }
        },

        created() {
            this.fetchCards();
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
