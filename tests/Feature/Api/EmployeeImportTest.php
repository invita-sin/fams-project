<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeImportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('super_admin');

        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    public function test_can_import_employees_from_csv(): void
    {
        $csvContent = "employee_code,full_name,email,phone,status\n";
        $csvContent .= "EMP001,John Doe,john@example.com,08123456789,active\n";
        $csvContent .= "EMP002,Jane Doe,jane@example.com,08987654321,active\n";

        $file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->post('/api/v1/employees/import', [
                'file' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'imported' => 2,
                    'total' => 2,
                ],
            ]);
    }

    public function test_cannot_import_without_file(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/employees/import', []);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_cannot_import(): void
    {
        $response = $this->postJson('/api/v1/employees/import', []);
        $response->assertStatus(401);
    }
}
