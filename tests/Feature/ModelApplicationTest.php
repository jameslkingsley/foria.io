<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelApplicationTest extends TestCase
{
    /** @test */
    public function canSubmitDetails()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post('/api/model-application', [
                'gender' => 'male',
                'date_of_birth' => now(),
                '_token' => $this->token,
                'nicknames' => 'Kingsley',
                'country' => 'United Kingdom',
                'full_name' => 'James Kingsley',
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function canSubmitProofOfAge()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post('/api/model-application', [
                'gender' => 'male',
                'date_of_birth' => now(),
                '_token' => $this->token,
                'nicknames' => 'Kingsley',
                'country' => 'United Kingdom',
                'full_name' => 'James Kingsley',
            ]);

        $response->assertStatus(200);
        $application = json_decode($response->getContent());

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post('/api/model-application/id', [
                '_token' => $this->token,
                'application_id' => $application->id,
                'photo_id' => UploadedFile::fake()->image('photo_id.jpg'),
                'photo_self' => UploadedFile::fake()->image('photo_self.jpg'),
            ]);

        $response->assertStatus(200);
    }
}
