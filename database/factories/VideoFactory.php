<?php

use App\Models\User;
use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $key = str_random(10);
    $requireSubscription = $faker->boolean;

    return [
        'user_id' => User::where('is_model', true)->inRandomOrder()->first()->id,
        'name' => $faker->city,
        'key' => $key,
        'path' => 'https://07-lvl3-pdl.vimeocdn.com/01/2017/3/85087990/222445922.mp4?expires=1505350936&token=04bf242217657de5a2615',
        'required_subscription' => $requireSubscription ? $faker->randomElement(['bronze', 'silver', 'gold']) : null,
        'token_price' => $requireSubscription ? null : $faker->numberBetween(0, 10000),
    ];
});
