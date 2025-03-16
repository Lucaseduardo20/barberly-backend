<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;

class AppointmentService
{
    public function done($id, $payment_method): void
    {
        $appointment = Appointment::find($id);
        $appointment->status = AppointmentStatus::DONE->value;
        $appointment->payment_method = $payment_method;
        $appointment->save();
    }

    public function cancel($id, $reason): void
    {
        $appointment = Appointment::find($id);
        $appointment->status = AppointmentStatus::CANCELED->value;
        $appointment->reason = $reason;
        $appointment->delete();
    }
}
