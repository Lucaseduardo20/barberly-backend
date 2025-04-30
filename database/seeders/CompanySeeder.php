<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Enums\RoleEnum;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Company::create([
            'name' => 'Barbearia do Lucas',
            'address' => 'Rua Dom Bento Pickel, 449',
            'tel' => '1139514274'
        ]);
    }
}