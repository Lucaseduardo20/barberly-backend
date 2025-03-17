<?php

namespace App\Services;

use App\Data\AppointmentData;
use App\Models\User;

class UserService
{
    public function preview(User $user)
    {
        return [
            'next_appointments' => $user->appointments()
//                ->where('appointment_date', '>', now())
                ->orderBy('appointment_date', 'asc')
                ->limit(2)
                ->get()
                ->map(fn($appointment) => AppointmentData::fromAppointment($appointment)),

            'commission' => $user->f_commission,

            'total_week_appointments' => $user->appointments()
                ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->where('status', 'pending')
                ->count()
        ];
    }
}
