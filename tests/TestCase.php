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
        $this->token = str_random(20);

        // $this->seed();
    }
}
