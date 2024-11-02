<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Appointment;
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

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'service_id' => Service::factory(),
            'user_id' => User::factory(),
            'appointment_date' => Carbon::today()->addDays(rand(1, 30)),
            'appointment_time' => Carbon::now()->addHours(rand(1, 8))->format('H:i:s'),
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'canceled']),
        ];
    }
}
