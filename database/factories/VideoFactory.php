<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(App\Models\Video::class, function (Faker $faker) {
    static $password;

    return [
        'user_id' => 1,
        'name' => 'TestVideo',
        'key' => 'TestVideo',
    ];
});
