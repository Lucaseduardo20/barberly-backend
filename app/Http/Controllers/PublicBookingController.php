<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AvailableSchedule;
use App\Models\Service;
use App\Support\TenantResolver;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    public function __construct(private readonly TenantResolver $tenantResolver)
    {
    }

    public function company(Request $request): JsonResponse
    {
        $company = $this->tenantResolver->resolveCompany($request);

        return response()->json([
            'id' => $company->id,
            'name' => $company->name,
            'address' => $company->address,
            'tel' => $company->tel,
        ]);
    }

    public function services(Request $request): JsonResponse
    {
        $company = $this->tenantResolver->resolveCompany($request);

        return response()->json([
            'data' => Service::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'duration']),
        ]);
    }

    public function availableTimes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'service_ids' => ['required'],
        ]);

        $company = $this->tenantResolver->resolveCompany($request);
        $serviceIds = $this->parseServiceIds($validated['service_ids']);
        $duration = Service::query()
            ->where('company_id', $company->id)
            ->whereIn('id', $serviceIds)
            ->sum('duration');

        if ($duration <= 0 || count($serviceIds) === 0) {
            return response()->json(['times' => []]);
        }

        $schedules = AvailableSchedule::query()
            ->where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->whereHas('user', fn ($query) => $query
                ->where('company_id', $company->id)
                ->where('role', 'barber'))
            ->orderBy('start_time')
            ->get();

        $appointments = Appointment::query()
            ->where('user_id', $validated['employee_id'])
            ->where('appointment_date', $validated['date'])
            ->where('status', '!=', AppointmentStatus::CANCELED->value)
            ->get(['appointment_time', 'end_time']);

        $times = [];

        foreach ($schedules as $schedule) {
            $cursor = Carbon::parse($schedule->date.' '.$schedule->start_time);
            $scheduleEnd = Carbon::parse($schedule->date.' '.$schedule->end_time);

            while ($cursor->copy()->addMinutes($duration)->lte($scheduleEnd)) {
                $slotStart = $cursor->format('H:i:s');
                $slotEnd = $cursor->copy()->addMinutes($duration)->format('H:i:s');

                $hasConflict = $appointments->contains(fn ($appointment) =>
                    $appointment->appointment_time < $slotEnd && $appointment->end_time > $slotStart
                );

                if (!$hasConflict && $cursor->isFuture()) {
                    $times[] = $cursor->format('H:i');
                }

                $cursor->addMinutes(15);
            }
        }

        return response()->json(['times' => array_values(array_unique($times))]);
    }

    private function parseServiceIds(array|string $serviceIds): array
    {
        if (is_array($serviceIds)) {
            return array_values(array_filter(array_map('intval', $serviceIds)));
        }

        return array_values(array_filter(array_map('intval', explode(',', $serviceIds))));
    }
}
