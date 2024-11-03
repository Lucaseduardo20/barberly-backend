<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ServiceData extends Data
{
    public function __construct(
        public int $price,
        public string $name
    )
    {}
}
