<template>
    <div class="token-checkout">
        <div v-if="user.has_card_on_file">
            <form method="post" id="payment-form" action="/tokens" @submit.prevent="pay">
                <input type="hidden" name="package_id" v-model="packageId">

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
                    <span class="token-checkout-subtitle">
                        Using card on file
                        <a href="/settings/#billing" class="is-pulled-right">Change</a>
                    </span>

                    <span class="token-checkout-card-brand">{{ user.card_brand }}</span>
                    <span class="token-checkout-card-number" v-html="formatLastFour(user.card_last_four)"></span>

                    <div class="block m-t-3">
                        <div class="columns">
                            <div class="column">
                                <small>
                                    All purchases are final and cannot be refunded. <a>Terms &amp; Conditions</a>.
                                </small>
                            </div>

                            <div class="column">
                                <button :class="submitButtonClasses" :disabled="buttonState">Agree &amp; Pay {{ cost | currency }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="w100 has-text-centered p-t-5 p-b-5" v-else>
            <p>Before you can purchase tokens, you need to add a card to your account. Click the button below.</p>
            <a href="/settings/#billing" class="button is-primary m-t-3 m-b-3">Add a card</a>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                packageId: 1,
                packages: [],
                buttonState: false
            };
        },

        computed: {
            cost() {
                return (! this.packages.length) ? 0 : this.packages.find(
                    p => p.id == this.packageId
                ).cost;
            },

            submitButtonClasses() {
                return {
                    'button': true,
                    'is-primary': true,
                    'is-pulled-right': true,
                    'is-loading': this.buttonState
                };
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

                axios.post('/tokens', { package_id: this.packageId }).then(r => {
                    this.$toast.open({
                        message: r.data.message,
                        type: `is-${r.data.style}`
                    });

                    this.buttonState = false;
                });
            }
        },

        created() {
            axios.get('/tokens/packages').then(
                r => this.packages = r.data
            );
        },

        mounted() {
            //
        }
    }
</script>
