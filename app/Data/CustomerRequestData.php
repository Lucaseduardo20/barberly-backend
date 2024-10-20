<?php

namespace App\Data;
use Spatie\LaravelData\Data;

class CustomerRequestData extends Data
{
    public function __construct(
        public string $name,

        public string $email,

        public string $phone,
    )
    {}
}
