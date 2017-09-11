<?php

use Faker\Generator as Faker;

$factory->define(App\Models\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'avatar' => $faker->imageUrl(),
        'remember_token' => str_random(10),
        'tokens' => $faker->numberBetween(0, 100000),
        'is_model' => $faker->boolean
    ];
});
