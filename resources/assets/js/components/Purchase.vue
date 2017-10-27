<template>
    <div v-if="loaded">
        <div v-if="loggedIn">
            <span class="button-tag" v-if="insufficientFunds && ! purchased">
                You don't have enough tokens
            </span>

            <button
                @click.prevent="buy"
                :class="classes"
                :disabled="disabled">

                <span class="icon" style="margin-left: -4px" v-if="purchased">
                    <i class="material-icons">check</i>
                </span>

                <span class="icon" style="margin-left: -4px" v-else>
                    <i class="material-icons">local_play</i>
                </span>

                <span v-if="purchased">
                    Purchased
                </span>

                <span v-else>
                    {{ amount | locale }} Tokens
                </span>
            </button>
        </div>

        <div v-else>
            <span class="button-tag">
                Login to watch the full video
            </span>

            <a :href="'/login/?ref=' + reference" class="button is-primary">
                <span>Login</span>
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            amount: { type: Number, default: 0 },
            reference: { type: String }
        },

        data() {
            return {
                loaded: false,
                purchase: null,
                purchasing: false,
                tokenTotal: Foria.user ? Foria.user.tokens : 0
            };
        },

        computed: {
            classes() {
                return {
                    'button': true,
                    'is-primary': ! this.purchased,
                    'is-success': this.purchased,
                    'is-loading': this.purchasing,
                    'is-solid': this.purchased
                };
            },

            purchased() {
                return this.purchase !== null;
            },

            disabled() {
                return this.purchased || this.purchasing || this.insufficientFunds;
            },

            insufficientFunds() {
                return this.tokenTotal < this.amount;
            },

            loggedIn() {
                return Foria.user !== null;
            }
        },

        methods: {
            buy() {
                // Don't purchase if already bought
                if (this.purchase) return;

                this.purchasing = true;

                ajax.post(`/api/purchase/${this.reference}`)
                    .then(r => {
                        if (r.data.status == 'success') {
                            this.$emit('success', r.data.data);

                            this.fetch().then(r => {
                                this.purchasing = false;
                            });
                        } else {
                            this.purchasing = false;

                            this.$toast.open({
                                message: r.data.data,
                                type: 'is-danger',
                                duration: 4000
                            });
                        }
                    });
            },

            fetch() {
                return ajax.get(`/api/purchase/${this.reference}`)
                    .then(r => {
                        this.loaded = true;
                        this.purchase = r.data.purchase;
                    })
                    .catch(e => {
                        this.loaded = true;
                    });
            }
        },

        created() {
            this.fetch().then(r => {
                if (this.insufficientFunds && !this.purchased) {
                    ForiaEvent.fire('TokensHint', this.amount);
                }
            });

            ForiaEvent.listen('TokensPurchased', e => {
                this.tokenTotal = e.total;
            });
        }
    }
</script>
