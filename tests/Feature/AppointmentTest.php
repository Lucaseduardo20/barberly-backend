<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_appointments()
    {
        $user = User::factory()->create();
        Appointment::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user, 'api')
            ->getJson(route('appointments.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'customer' => ['id', 'name', 'email'],
                    'appointment_date',
                    'appointment_time',
                    'status',
                    'services',
                    'amount',
                    'estimated_time',
                    'assigned_to',
                    'payment_method',
                ]
            ]);
    }
}
