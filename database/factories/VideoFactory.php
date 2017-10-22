<?php

use App\Models\User;
use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $requireSubscription = $faker->boolean;

    return [
        'key' => '8ef9136ef9abde83e29e65a9086bcd20',
        'name' => $faker->city,
        'user_id' => User::where('is_model', true)->inRandomOrder()->first()->id,
        'token_price' => $requireSubscription ? null : $faker->numberBetween(0, 10000),
        'has_processed' => true,
        'required_subscription' => $requireSubscription ? $faker->randomElement(['bronze', 'silver', 'gold']) : null,
        'privacy' => $faker->randomElement(['private', 'public'])
    ];
});
