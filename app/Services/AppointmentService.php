<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;

class AppointmentService
{
    public function done($id, $payment_method): void
    {
        $appointment = Appointment::find($id);
        $appointment->status = AppointmentStatus::DONE->value;
        $appointment->payment_method = $payment_method;
        $this->pay_collaborator($appointment->user, $appointment->amount);
        $appointment->save();
    }

    public function cancel($id, $reason): void
    {
        $appointment = Appointment::find($id);
        $appointment->status = AppointmentStatus::CANCELED->value;
        $appointment->reason = $reason;
        $appointment->delete();
    }

    public function pay_collaborator(User $user, int $amount)
    {
        $user->increment('commission', $amount * ($user->percentage / 100));
    }
}
