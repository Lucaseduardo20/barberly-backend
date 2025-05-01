<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class BarberData extends Data
{
    public function __construct(
        public string $name,
        public string $tel,
    )
    {
    }

    public static function fromUser (User $user): self
    {
        return new self (
            name: $user->name,
            tel: $user->tel,
        );
    }
}
