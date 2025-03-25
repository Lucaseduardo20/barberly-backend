<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class AppointmentRequestData extends Data
{
    public function __construct(
        #[Required, Exists('customers', 'id')]
        public int $customer_id,

        #[Required, Exists('users', 'id')]
        public int $employee_id,

        #[Required, ArrayType, Min(1)]
        public array $services,

        #[Required, Date]
        public string $appointment_date,

        #[Required]
        public string $appointment_time,

        #[Required]
        public float $amount,

        #[Required]
        public int $duration,

        public ?string $payment_method
    ) {
    }
}
