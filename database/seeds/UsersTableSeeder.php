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
            'remember_token' => str_random(10)
        ]);

        factory(User::class, 30)->create();
    }
}
