<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,
        DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->user = User::first();
        $this->userDisposable = User::create([
            'name' => 'KingsleyDisposable',
            'email' => 'jlkingsley97_disposable@gmail.com',
            'password' => bcrypt('password'),
            'is_model' => true,
            'remember_token' => str_random(10),
            'stripe_id' => 'cus_BOXtJ54MeJrMvy',
            'card_brand' => 'Visa',
            'card_last_four' => '4242',
            'tokens' => 100000,
        ]);

        $this->token = str_random(20);

        // $this->seed();
    }
}
