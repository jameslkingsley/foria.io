<?php

use App\Models\User;
use App\Models\Video;
use App\Models\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'model_id' => Video::inRandomOrder()->first()->id,
        'model_type' => Video::class,
        'body' => $faker->sentence
    ];
});
