<?php

use App\Models\TokenPackage;
use Illuminate\Database\Seeder;

class TokenPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TokenPackage::create(['token_count' => 50, 'cost' => 1000]);
        TokenPackage::create(['token_count' => 100, 'cost' => 2000]);
        TokenPackage::create(['token_count' => 250, 'cost' => 5000]);
        TokenPackage::create(['token_count' => 500, 'cost' => 10000]);
    }
}
