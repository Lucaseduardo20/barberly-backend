<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Appointment;
use App\Enums\AppointmentStatus;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    public function configure(): static
    {
        return $this->afterCreating(function (Appointment $appointment) {
            $service = Service::factory()->create();

            $appointment->services()->attach($service->id, [
                'duration' => $service->duration,
            ]);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = Carbon::now()->addHours(rand(1, 8))->startOfMinute();
        $estimatedTime = $this->faker->randomElement([20, 30, 40, 60]);

        return [
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
            'appointment_date' => Carbon::today()->addDays(rand(1, 30)),
            'appointment_time' => $startTime->format('H:i:s'),
            'estimated_time' => $estimatedTime,
            'status' => $this->faker->randomElement(array_column(AppointmentStatus::cases(), 'value')),
            'amount' => $this->faker->randomElement([30, 50, 75, 100, 140]),
            'end_time' => $startTime->copy()->addMinutes($estimatedTime)->format('H:i:s'),
        ];
    }
}
