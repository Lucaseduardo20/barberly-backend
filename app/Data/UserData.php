<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $tel,
        public string $role,
        public string $company_name,
        public string $commission,
    )
    {
    }

    public static function fromUser (User $user): self
    {
        return new self (
            id: $user->id,
            name: $user->name,
            email: $user->email,
            tel: $user->tel,
            role: $user->f_role,
            company_name: $user->company->name,
            commission: $user->f_commission,
        );
    }
}
