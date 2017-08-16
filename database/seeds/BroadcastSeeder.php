<?php

use App\Models\Broadcast;
use Illuminate\Database\Seeder;

class BroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Broadcast::class, 50)->create();
    }
}
