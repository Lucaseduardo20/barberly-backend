<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
//    use RefreshDatabase;

    public function test_index_appointments()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();

        $appointment = Appointment::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
        ]);

        $response = $this->getJson(route('appointments.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'user' => ['id', 'name'],
                    'service' => ['id', 'name'],
                    'appointment_time',
                ]
            ]);
    }
}
