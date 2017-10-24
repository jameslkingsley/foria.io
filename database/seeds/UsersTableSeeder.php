<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $developer = tap(factory(User::class)->create())->update([
            'name' => 'Kingsley',
            'email' => 'jlkingsley97@gmail.com',
            'password' => bcrypt('password'),
            'is_model' => true,
            'remember_token' => str_random(10),
            'stripe_id' => 'cus_BOXtJ54MeJrMvy',
            'card_brand' => 'Visa',
            'card_last_four' => '4242',
            'tokens' => 100000,
        ]);

        $user = User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'remember_token' => str_random(10),
            'is_model' => false,
            'stripe_id' => 'cus_BOXtJ54MeJrMvy',
            'card_brand' => 'Visa',
            'card_last_four' => '4242',
            'tokens' => 1000000
        ]);

        factory(User::class, 30)->create()->each(function ($user) use ($developer) {
            $user->follow($developer);
        });
    }
}
