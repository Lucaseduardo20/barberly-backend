<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\AvailableSchedule;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_barbers_are_filtered_by_company(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $barber = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'barber',
        ]);
        User::factory()->create([
            'company_id' => $otherCompany->id,
            'role' => 'barber',
        ]);

        $response = $this->getJson('/api/customer/barbers?company_id='.$company->id);

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $barber->id]);
    }

    public function test_public_customer_registration_assigns_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->postJson('/api/customer/register', [
            'company_id' => $company->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'phone' => '(11) 99999-9999',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('customers', [
            'email' => 'cliente@example.com',
            'company_id' => $company->id,
        ]);
    }

    public function test_public_appointment_rejects_services_from_another_company(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $barber = User::factory()->create(['company_id' => $company->id, 'role' => 'barber']);
        $service = Service::factory()->create(['company_id' => $otherCompany->id]);
        AvailableSchedule::query()->create([
            'employee_id' => $barber->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '12:00',
        ]);

        $response = $this->postJson('/api/appointments', [
            'customer_id' => $customer->id,
            'employee_id' => $barber->id,
            'services' => [
                ['id' => $service->id, 'duration' => $service->duration],
            ],
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'amount' => 1,
            'duration' => 1,
            'payment_method' => null,
        ]);

        $response->assertStatus(422);
    }

    public function test_public_appointment_uses_company_services_to_calculate_amount(): void
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $barber = User::factory()->create(['company_id' => $company->id, 'role' => 'barber']);
        $service = Service::factory()->create([
            'company_id' => $company->id,
            'price' => 50,
            'duration' => 40,
        ]);
        AvailableSchedule::query()->create([
            'employee_id' => $barber->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '12:00',
        ]);

        $response = $this->postJson('/api/appointments', [
            'customer_id' => $customer->id,
            'employee_id' => $barber->id,
            'services' => [
                ['id' => $service->id, 'duration' => 999],
            ],
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'amount' => 1,
            'duration' => 1,
            'payment_method' => null,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('appointments', [
            'customer_id' => $customer->id,
            'user_id' => $barber->id,
            'amount' => 50,
            'estimated_time' => 40,
        ]);
    }
}
