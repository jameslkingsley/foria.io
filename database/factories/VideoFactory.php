<?php

use App\Models\User;
use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $key = str_random(10);
    $requireSubscription = $faker->boolean;

    return [
        'key' => $key,
        'name' => $faker->city,
        'permalink' => str_random(11),
        'user_id' => User::where('is_model', true)->inRandomOrder()->first()->id,
        'token_price' => $requireSubscription ? null : $faker->numberBetween(0, 10000),
        'path' => 'videos/6c8a07b274b682c1261828f904e3436c/transcoded/6c8a07b274b682c1261828f904e3436c.mp4',
        'required_subscription' => $requireSubscription ? $faker->randomElement(['bronze', 'silver', 'gold']) : null,
    ];
});
