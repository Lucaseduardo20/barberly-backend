<?php

namespace App\Data;

use App\Models\AvailableSchedule;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{Date, DateFormat, Required, After};

class AvailableScheduleData extends Data
{
    public function __construct(
        #[Required, Date]
        public string $date,

        #[Required, DateFormat('H:i')]
        public ?string $start_time,

        #[Required, DateFormat('H:i'), After('start_time')]
        public ?string $end_time,

        public Collection|array $periods


    ) {}

    public static function fromSchedule(AvailableSchedule $schedule): self
    {
        return new self(
            date: $schedule->date,
            start_time: $schedule->start_time,
            end_time: $schedule->end_time,
            periods: ['id' => $schedule->id ?? null, 'end' => $schedule->end_time, 'start' => $schedule->start_time]
        );
    }

    public static function fromData(array $data): self
    {
        return new self(
            date: $data['date'],
            start_time: $data['start_time'] ?? null,
            end_time: $data['end_time'] ?? null,
            periods: $data['periods'] ?? ['id' => $data['id'] ?? null, 'end' => $data['end_time'], 'start' => $data['start_time']]
        );
    }
}

