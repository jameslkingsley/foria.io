<?php

use App\Models\User;
use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $requireSubscription = $faker->boolean;

    $thumbnails = [
        "https://foria.s3.eu-west-2.amazonaws.com/videos/8ef9136ef9abde83e29e65a9086bcd20/thumbnails/0.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/8ef9136ef9abde83e29e65a9086bcd20/thumbnails/10.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/8ef9136ef9abde83e29e65a9086bcd20/thumbnails/20.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/8ef9136ef9abde83e29e65a9086bcd20/thumbnails/30.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/8ef9136ef9abde83e29e65a9086bcd20/thumbnails/40.jpg",
        "https://foria.s3.eu-west-2.amazonaws.com/videos/8ef9136ef9abde83e29e65a9086bcd20/thumbnails/50.jpg"
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
        'key' => '8ef9136ef9abde83e29e65a9086bcd20',
        'privacy' => $faker->randomElement(['private', 'public']),
        'user_id' => $faker->boolean ? User::where('is_model', true)->inRandomOrder()->first()->id : User::first()->id,
        'token_price' => $requireSubscription ? null : $faker->numberBetween(0, 10000),
        'required_subscription' => $requireSubscription ? $faker->randomElement(['bronze', 'silver', 'gold']) : null,
    ];
});
