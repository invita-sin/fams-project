<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::factory()->create([
            'email' => 'hr@fams.test',
        ]);
        $this->admin->assignRole('hr_admin');

        $this->token = $this->admin->createToken('api-token')->plainTextToken;
    }

    public function test_can_create_employee(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->withToken($this->token)->postJson('/api/v1/employees', [
            'employee_code' => 'EMP001',
            'full_name' => 'John Doe',
            'phone' => '08123456789',
            'branch_id' => $branch->id,
            'status' => 'active',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('employees', [
            'employee_code' => 'EMP001',
            'full_name' => 'John Doe',
        ]);
    }

    public function test_can_list_employees(): void
    {
        Employee::factory()->count(3)->create();

        $response = $this->withToken($this->token)->getJson('/api/v1/employees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);
    }

    public function test_can_show_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->withToken($this->token)->getJson("/api/v1/employees/{$employee->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_can_update_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->withToken($this->token)->putJson("/api/v1/employees/{$employee->id}", [
            'employee_code' => $employee->employee_code,
            'full_name' => 'Jane Doe Updated',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'full_name' => 'Jane Doe Updated',
        ]);
    }

    public function test_can_deactivate_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->withToken($this->token)->deleteJson("/api/v1/employees/{$employee->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'status' => 'inactive',
        ]);
    }

    public function test_unauthenticated_cannot_access_employees(): void
    {
        $response = $this->getJson('/api/v1/employees');
        $response->assertStatus(401);
    }
}
