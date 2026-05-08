<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AvailableSchedule;
use App\Enums\AppointmentStatus;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentSchedulingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_appointment_requires_barber_availability(): void
    {
        [$customer, $barber, $service] = $this->createSchedulingFixture();

        $response = $this->postJson('/api/appointments', $this->appointmentPayload($customer, $barber, $service));

        $response->assertStatus(422);
    }

    public function test_public_appointment_rejects_time_conflict(): void
    {
        [$customer, $barber, $service] = $this->createSchedulingFixture();
        $date = now()->addDay()->format('Y-m-d');

        AvailableSchedule::query()->create([
            'employee_id' => $barber->id,
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '12:00',
        ]);

        Appointment::factory()->create([
            'customer_id' => $customer->id,
            'user_id' => $barber->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00',
            'end_time' => '10:40',
            'estimated_time' => 40,
            'status' => AppointmentStatus::PENDING_CONFIRMATION->value,
        ]);

        $response = $this->postJson('/api/appointments', $this->appointmentPayload($customer, $barber, $service));

        $response->assertStatus(422);
    }

    public function test_public_appointment_can_use_back_to_back_slot(): void
    {
        [$customer, $barber, $service] = $this->createSchedulingFixture();
        $date = now()->addDay()->format('Y-m-d');

        AvailableSchedule::query()->create([
            'employee_id' => $barber->id,
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '12:00',
        ]);

        Appointment::factory()->create([
            'customer_id' => $customer->id,
            'user_id' => $barber->id,
            'appointment_date' => $date,
            'appointment_time' => '09:20',
            'end_time' => '10:00',
            'estimated_time' => 40,
            'status' => AppointmentStatus::PENDING_CONFIRMATION->value,
        ]);

        $response = $this->postJson('/api/appointments', $this->appointmentPayload($customer, $barber, $service));

        $response->assertCreated();
    }

    private function createSchedulingFixture(): array
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $barber = User::factory()->create(['company_id' => $company->id, 'role' => 'barber']);
        $service = Service::factory()->create([
            'company_id' => $company->id,
            'price' => 50,
            'duration' => 40,
        ]);

        return [$customer, $barber, $service];
    }

    private function appointmentPayload(Customer $customer, User $barber, Service $service): array
    {
        return [
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
        ];
    }
}
