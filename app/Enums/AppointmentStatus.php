<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case SCHEDULED = 'scheduled';

    case PENDING_CONFIRMATION = 'pending';
    case CANCELED = 'canceled';

    case DONE = 'done';

    public static function isDone(string $status): bool
    {
        return $status === self::DONE->value;
    }
}
