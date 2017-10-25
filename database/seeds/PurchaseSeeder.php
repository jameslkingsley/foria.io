<?php

use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        auth()->login(User::first());

        $videos = Video::wherePrivacy('public')
            ->whereHasProcessed(true)
            ->where('user_id', '!=', auth()->user()->id)
            ->where('token_price', '>', 0)
            ->inRandomOrder()
            ->get();

        foreach ($videos as $video) {
            $video->purchase();
        }
    }
}
