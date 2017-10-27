<?php

use App\Models\User;
use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $requireSubscription = $faker->boolean;

    $thumbnails = [
        "https://foria.s3.eu-west-2.amazonaws.com/videos/1d88b537748e6774baff934f9546920f/thumbnails/0.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/1d88b537748e6774baff934f9546920f/thumbnails/10.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/1d88b537748e6774baff934f9546920f/thumbnails/20.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/1d88b537748e6774baff934f9546920f/thumbnails/30.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/1d88b537748e6774baff934f9546920f/thumbnails/40.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/1d88b537748e6774baff934f9546920f/thumbnails/50.jpg"
    ];

    return [
        'width' => 1280,
        'height' => 720,
        'duration' => 60,
        'frame_rate' => 24,
        'name' => $faker->city,
        'has_processed' => true,
        'thumbnails' => json_encode($thumbnails),
        'views' => $faker->numberBetween(0, 100000),
        'key' => '1d88b537748e6774baff934f9546920f',
        'private_key' => str_random(32),
        'privacy' => $faker->randomElement(['private', 'public']),
        'token_price' => $requireSubscription ? null : $faker->numberBetween(0, 1000),
        'required_subscription' => $requireSubscription ? $faker->randomElement(['bronze', 'silver', 'gold']) : null,
        'user_id' => User::where('is_model', true)->inRandomOrder()->first()->id,
    ];
});
