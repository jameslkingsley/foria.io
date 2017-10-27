<?php

use Faker\Generator as Faker;

$factory->define(App\Models\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'avatar' => $faker->imageUrl(),
        'bio' => $faker->sentence,
        'remember_token' => str_random(10),
        'tokens' => $faker->numberBetween(1000, 100000),
        'is_model' => $faker->boolean,
        'stripe_id' => 'cus_BOXtJ54MeJrMvy',
        'card_brand' => 'Visa',
        'card_last_four' => '4242'
    ];
});
