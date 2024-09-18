<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class AppointmentRequestData extends Data
{
    public function __construct(

        #[Required, Exists('customers', 'id')]
        public int $customer_id,

        #[Required, Exists('users', 'id')]
        public int $employee_id,

        #[Required, Exists('services', 'id')]
        public int $service_id,

        #[Required, Date]
        public string $appointment_date,

        #[Required]
        public string $appointment_time,

        #[Required]
        public string $status
    ) {
    }
}
