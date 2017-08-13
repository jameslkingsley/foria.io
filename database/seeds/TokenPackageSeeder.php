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
        TokenPackage::create(['token_count' => 50, 'cost' => 500, 'stripe_fee' => 35]);
        TokenPackage::create(['token_count' => 100, 'cost' => 1000, 'stripe_fee' => 49]);
        TokenPackage::create(['token_count' => 250, 'cost' => 2500, 'stripe_fee' => 93]);
        TokenPackage::create(['token_count' => 500, 'cost' => 5000, 'stripe_fee' => 165]);
    }
}
