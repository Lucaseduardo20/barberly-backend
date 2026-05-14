<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AvailableSchedule;
use App\Models\Company;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_company_returns_resolved_tenant(): void
    {
        $company = Company::factory()->create(['name' => 'Tenant Barber']);

        $response = $this->getJson('/api/public/company?company_id='.$company->id);

        $response
            ->assertOk()
            ->assertJsonFragment(['id' => $company->id, 'name' => 'Tenant Barber']);
    }

    public function test_public_services_are_filtered_by_tenant(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $service = Service::factory()->create(['company_id' => $company->id]);
        Service::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->getJson('/api/public/services?company_id='.$company->id);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $service->id]);
    }

    public function test_public_available_times_respect_schedules_and_conflicts(): void
    {
        $company = Company::factory()->create();
        $barber = User::factory()->create(['company_id' => $company->id, 'role' => 'barber']);
        $service = Service::factory()->create([
            'company_id' => $company->id,
            'duration' => 30,
        ]);
        $date = now()->addDay()->format('Y-m-d');

        AvailableSchedule::query()->create([
            'employee_id' => $barber->id,
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        Appointment::factory()->create([
            'user_id' => $barber->id,
            'appointment_date' => $date,
            'appointment_time' => '09:15',
            'end_time' => '09:45',
            'estimated_time' => 30,
            'status' => AppointmentStatus::PENDING_CONFIRMATION->value,
        ]);

        $response = $this->getJson('/api/public/available-times?company_id='.$company->id.'&employee_id='.$barber->id.'&date='.$date.'&service_ids='.$service->id);

        $response
            ->assertOk()
            ->assertJson(['times' => []]);
    }
}
