<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TokenPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TokenTest extends TestCase
{
    /** @test */
    public function cannotPurchaseTokensWhenGuest()
    {
        $this->assertGuest();

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post('/api/tokens', [
                '_token' => $this->token,
                'package_id' => TokenPackage::first()->id
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function canPurchaseTokensWhenAuthenticated()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $package = TokenPackage::first();

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post('/api/tokens', [
                '_token' => $this->token,
                'package_id' => $package->id
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'style' => 'success',
                'amount' => $package->token_count,
                'total' => auth()->user()->fresh()->tokens,
                'message' => "{$package->token_count} tokens added to your account",
            ]);
    }
}
