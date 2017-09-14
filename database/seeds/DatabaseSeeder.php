<?php

use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(TokenPackageSeeder::class);
        $this->call(VideoSeeder::class);

        factory(Comment::class, 50)->create();
    }
}
