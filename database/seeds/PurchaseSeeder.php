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
        $users = User::whereNotNull('stripe_id')
            ->inRandomOrder()
            ->take(15)
            ->get();

        foreach ($users as $user) {
            auth()->logout();
            auth()->login($user);

            $videos = Video::wherePrivacy('public')
                ->whereHasProcessed(true)
                ->where('user_id', '!=', $user->id)
                ->where('token_price', '>', 0)
                ->where('token_price', '<', $user->tokens)
                ->inRandomOrder()
                ->get();

            foreach ($videos as $video) {
                $video->purchase();
            }
        }
    }
}
