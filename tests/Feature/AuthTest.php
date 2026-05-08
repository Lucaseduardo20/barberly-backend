<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_can_create_company_owner(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Lucas Admin',
            'email' => 'lucas@example.com',
            'password' => '123123',
            'tel' => '11999999999',
            'company_name' => 'Bigods Barber',
            'company_address' => 'Rua Teste, 123',
            'company_tel' => '1133333333',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role', 'company_name']]);

        $this->assertDatabaseHas('companies', ['name' => 'Bigods Barber']);
        $this->assertDatabaseHas('users', [
            'email' => 'lucas@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_register_can_attach_barber_to_existing_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->postJson('/api/register', [
            'company_id' => $company->id,
            'name' => 'Barbeiro',
            'email' => 'barbeiro@example.com',
            'password' => '123123',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'company_id' => $company->id,
            'email' => 'barbeiro@example.com',
            'role' => 'barber',
        ]);
    }

    public function test_login_returns_token_and_user(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('123123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => '123123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
    }
}
