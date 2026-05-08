<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function done(User $user, $id, $payment_method): void
    {
        $appointment = $this->findAppointmentForUser($user, $id);

        if (AppointmentStatus::isDone($appointment->status)) {
            throw ValidationException::withMessages([
                'id' => 'Agendamento ja foi concluido.',
            ]);
        }

        $appointment->status = AppointmentStatus::DONE->value;
        $appointment->payment_method = $payment_method;
        $this->pay_collaborator($appointment->user, $appointment->amount);
        $appointment->save();
    }

    public function cancel(User $user, $id, $reason): void
    {
        $appointment = $this->findAppointmentForUser($user, $id);
        $appointment->status = AppointmentStatus::CANCELED->value;
        $appointment->reason = $reason;
        $appointment->save();
        $appointment->delete();
    }

    public function pay_collaborator(User $user, float|int $amount)
    {
        $user->increment('commission', $amount * ($user->percentage / 100));
    }

    private function findAppointmentForUser(User $user, int $id): Appointment
    {
        return Appointment::query()
            ->whereHas('user', fn (Builder $query) => $query->where('company_id', $user->company_id))
            ->when(!$user->is_admin, fn (Builder $query) => $query->where('user_id', $user->id))
            ->findOrFail($id);
    }
}
