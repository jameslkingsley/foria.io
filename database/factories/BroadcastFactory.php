<?php

use App\Models\User;
use App\Models\Broadcast;
use Faker\Generator as Faker;

$factory->define(Broadcast::class, function (Faker $faker) {
    return [
        'topic' => $faker->sentence,
        'user_id' => User::inRandomOrder()->first()->id,
        'session_id' => '',
        'online' => $faker->boolean,
    ];
});
