<?php

use App\Models\User;
use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $key = str_random(10);
    $requireSubscription = $faker->boolean;

    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'name' => $faker->city,
        'key' => $key,
        'path' => "videos/$key/transcoded/$key.mp4",
        'required_subscription' => $requireSubscription ? $faker->randomElement(['bronze', 'silver', 'gold']) : null,
        'token_price' => $requireSubscription ? null : $faker->numberBetween(0, 10000),
    ];
});
