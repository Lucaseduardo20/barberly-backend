<?php

namespace App\Services;

use App\Models\AvailableSchedule;
use App\Data\AvailableScheduleData;
use Illuminate\Support\Facades\Auth;

class AvailableScheduleService
{
    public function listSchedules()
    {
        $user = Auth::user();
        return $user->availableSchedules()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('date')
            ->map(fn ($items, $date) => [
                'date' => $date,
                'periods' => $items->map(fn ($item) => [
                    'start' => $item->start_time,
                    'end' => $item->end_time,
                ])->values()
            ])
            ->values();
    }

    public function createSchedule(AvailableScheduleData $data)
    {

        $user = Auth::user();

        $conflict = AvailableSchedule::where('employee_id', $user->id)
            ->where('date', $data->date)
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data->start_time, $data->end_time])
                    ->orWhereBetween('end_time', [$data->start_time, $data->end_time]);
            })
            ->exists();

        if ($conflict) {
            return ['message' => 'Conflito de horários detectado.', 'status' => 422];
        }

        $schedule = $user->availableSchedules()->create([
            'employee_id' => $user->id,
            'date' => $data->date,
            'start_time' => $data->start_time,
            'end_time' => $data->end_time,
        ]);

        return ['message' => 'Horário cadastrado com sucesso!', 'data' => $schedule, 'status' => 201];
    }

    public function deleteSchedule(int $id)
    {
        $user = Auth::user();

        $schedule = AvailableSchedule::where('id', $id)->where('employee_id', $user->id)->first();

        if (!$schedule) {
            return ['error' => 'Horário não encontrado.', 'status' => 404];
        }

        $schedule->delete();

        return ['message' => 'Horário removido com sucesso.', 'status' => 200];
    }
}

