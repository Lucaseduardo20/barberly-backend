<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Service;
use App\Models\Company;
use App\Enums\RoleEnum;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $companyId = Company::query()->first()->id;

        Service::create([
            'company_id' => $companyId,
            'name' => 'Corte de Cabelo',
            'price' => 50,
            'duration' => 40
        ]);

        Service::create([
            'company_id' => $companyId,
            'name' => 'Barba',
            'price' => 30,
            'duration' => 20
        ]);

        Service::create([
            'company_id' => $companyId,
            'name' => 'Corte de Cabelo + Barba',
            'price' => 75,
            'duration' => 60
        ]);

        Service::create([
            'company_id' => $companyId,
            'name' => 'Progressiva',
            'price' => 100,
            'duration' => 60
        ]);

        Service::create([
            'company_id' => $companyId,
            'name' => 'Corte de Cabelo + Progressiva',
            'price' => 140,
            'duration' => 90
        ]);

        Service::create([
            'company_id' => $companyId,
            'name' => 'Botox',
            'price' => 80,
            'duration' => 40
        ]);

        Service::create([
            'company_id' => $companyId,
            'name' => 'Sobrancelha',
            'price' => 20,
            'duration' => 20
        ]);
    }
}
