<?php

namespace Tests\Feature\Api;

use App\Models\DocumentationPhoto;
use App\Models\Employee;
use App\Models\FieldDocumentation;
use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Employee $employee;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::factory()->create([
            'email' => 'admin@doc.test',
        ]);
        $this->user->assignRole('super_admin');

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    public function test_can_create_documentation(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/documentations', [
            'title' => 'Visit Client A',
            'description' => 'Discussed project requirements',
            'category' => 'visit',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'activity_date' => today()->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('field_documentations', [
            'title' => 'Visit Client A',
        ]);
    }

    public function test_can_list_documentations(): void
    {
        FieldDocumentation::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/documentations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);
    }

    public function test_can_show_documentation(): void
    {
        $documentation = FieldDocumentation::factory()->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/documentations/{$documentation->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_can_delete_documentation(): void
    {
        $documentation = FieldDocumentation::factory()->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->withToken($this->token)->deleteJson("/api/v1/documentations/{$documentation->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('field_documentations', [
            'id' => $documentation->id,
        ]);
    }

    public function test_can_upload_photo(): void
    {
        $documentation = FieldDocumentation::factory()->create([
            'employee_id' => $this->employee->id,
        ]);

        $mediaFile = MediaFile::factory()->create([
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->withToken($this->token)->postJson(
            "/api/v1/documentations/{$documentation->id}/photos",
            ['media_file_id' => $mediaFile->id]
        );

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('documentation_photos', [
            'documentation_id' => $documentation->id,
            'media_file_id' => $mediaFile->id,
        ]);
    }

    public function test_unauthenticated_cannot_access_documentations(): void
    {
        $response = $this->postJson('/api/v1/documentations', [
            'title' => 'Test',
        ]);

        $response->assertStatus(401);
    }
}
