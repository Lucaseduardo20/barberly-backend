<?php

namespace App\Enums;

enum RoleEnum: string
{
    case BARBER = 'Barbeiro';

    case ADMIN = 'Administrador';
    case MANAGER = 'Gerente';


    public static function fromUser(string $role): self
    {
        return match ($role) {
            'barber' => self::BARBER,
            'admin' => self::ADMIN,
            'manager' => self::MANAGER,
            default => throw new \InvalidArgumentException("Role inválida: $role"),
        };
    }
}
