<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Enums\RoleEnum;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'email' => 'admin@admin.com',
            'name' => 'admin',
            'password' => Hash::make('123123'),
            'tel' => '11954065757',
            'company_id' => Company::query()->first()->id,
            'role' => 'admin',
            'commission' => '0',
            'percentage' => 50
        ]);

        User::create([
            'email' => 'gabriel@barber.com',
            'name' => 'Gabriel Giacoboni',
            'password' => Hash::make('123123'),
            'tel' => '11954876765',
            'company_id' => Company::query()->first()->id,
            'role' => 'barber',
            'commission' => '0',
            'percentage' => 50
        ]);

        User::create([
            'email' => 'erick@barber.com',
            'name' => 'Erick Yan',
            'password' => Hash::make('123123'),
            'tel' => '11954876765',
            'company_id' => Company::query()->first()->id,
            'role' => 'barber',
            'commission' => '0',
            'percentage' => 50
        ]);
    }
}