<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelApplicationTest extends TestCase
{
    /** @test */
    public function canSubmitApplication()
    {
        $this->actingAs($this->userDisposable);
        $this->assertAuthenticated();

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post('/api/application', [
                'gender' => 'male',
                'date_of_birth' => now(),
                '_token' => $this->token,
                'nicknames' => 'Kingsley',
                'country' => 'United Kingdom',
                'full_name' => 'James Kingsley',
            ]);

        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $application = Application::findOrFail($data->id);

        $response = $this
            ->withSession(['_token' => $this->token])
            ->post('/api/application/id', [
                '_token' => $this->token,
                'application_id' => $application->id,
                'photo_id' => UploadedFile::fake()->image('photo_id.jpg'),
                'photo_self' => UploadedFile::fake()->image('photo_self.jpg'),
            ]);

        $response->assertStatus(200);

        $this->assertNotNull($application->fresh()->photo_id);
        $this->assertNotNull($application->fresh()->photo_self);

        Storage::cloud()->deleteDirectory("models/{$this->userDisposable->name}");
    }
}
