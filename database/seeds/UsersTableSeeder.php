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
        User::create([
            'name' => 'Kingsley',
            'email' => 'jlkingsley97@gmail.com',
            'password' => bcrypt('password'),
            'remember_token' => str_random(10),
            'is_model' => true,
            'stripe_id' => 'cus_BOXtJ54MeJrMvy',
            'card_brand' => 'Visa',
            'card_last_four' => '4242',
        ]);

        factory(User::class, 30)->create();
    }
}
