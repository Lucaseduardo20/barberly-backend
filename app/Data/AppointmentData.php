<?php

namespace App\Data;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Data;

class AppointmentData extends Data
{
    public function __construct(
        public int $id,
        public CustomerData $customer,
        public string $appointment_date,
        public string $appointment_time,
        public string $status,
        public array $services,
        public int $amount,
        public string $estimated_time,
        public ?string $assigned_to
    ) {}

    public static function fromAppointment(Appointment $appointment): self
    {
        return new self(
            id: $appointment->id,
            customer: CustomerData::from($appointment->customer),
            appointment_date: $appointment->f_appointment_date,
            appointment_time: $appointment->f_appointment_time,
            status: $appointment->status,
            services: ServiceData::fromData($appointment->services->toArray()),
            amount: $appointment->amount,
            estimated_time: $appointment->f_estimated_time,
            assigned_to: Auth::user()->is_admin ? $appointment->user->name : null
        );
    }
}
