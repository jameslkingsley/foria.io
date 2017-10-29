<?php

return [
    'app' => [
        'name' => config('app.name'),
        'emails' => [
            'support' => 'support@foria.io'
        ]
    ],

    'subscription' => [
        /**
         * Percentage paid out to the model.
         */
        'payout' => 0.6,

        /**
         * Available subscription plans.
         * Must also be setup in Stripe.
         */
        'plans' => [
            ['id' => 'bronze', 'title' => 'Bronze', 'price' => 499],
            ['id' => 'silver', 'title' => 'Silver', 'price' => 999],
            ['id' => 'gold', 'title' => 'Gold', 'price' => 2499],
        ]
    ],

    'tokens' => [
        /**
         * Percentage paid out to the model.
         */
        'payout' => 0.7,

        /**
         * Value of a single token in pence.
         */
        'value' => 10
    ]
];
