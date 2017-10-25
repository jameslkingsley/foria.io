<template>
    <div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>User</th>
                    <th align="right">Tokens</th>
                    <th align="right">Amount</th>
                    <th align="right">Payout</th>
                    <th align="right">Profit</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(purchase, index) in purchases">
                    <td v-text="purchase.id"></td>
                    <td v-text="purchase.name"></td>
                    <td v-text="purchase.user.name"></td>
                    <td align="right">{{ purchase.tokens | locale }}</td>
                    <td align="right">{{ purchase.amount | currency }}</td>
                    <td align="right">{{ purchase.payout | currency }}</td>
                    <td align="right">{{ purchase.profit | currency }}</td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan="3"></th>
                    <th align="right">{{ totalTokens | locale }}</th>
                    <th align="right">{{ totalAmount | currency }}</th>
                    <th align="right">{{ totalPayout | currency }}</th>
                    <th align="right">{{ totalProfit | currency }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                purchases: []
            };
        },

        computed: {
            totalTokens() {
                return collect(this.purchases).sum('tokens');
            },

            totalAmount() {
                return collect(this.purchases).sum('amount');
            },

            totalPayout() {
                return collect(this.purchases).sum('payout');
            },

            totalProfit() {
                return collect(this.purchases).sum('profit');
            }
        },

        methods: {
            fetch() {
                ajax.get('/api/dashboard/revenue')
                    .then(r => this.purchases = r.data);
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
