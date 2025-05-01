<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Service;
use App\Enums\RoleEnum;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Service::create([
            'name' => 'Corte de Cabelo',
            'price' => 50,
            'duration' => 40
        ]);

        Service::create([
            'name' => 'Barba',
            'price' => 30,
            'duration' => 20
        ]);

        Service::create([
            'name' => 'Corte de Cabelo + Barba',
            'price' => 75,
            'duration' => 60
        ]);

        Service::create([
            'name' => 'Progressiva',
            'price' => 100,
            'duration' => 60
        ]);

        Service::create([
            'name' => 'Corte de Cabelo + Progressiva',
            'price' => 140,
            'duration' => 90
        ]);

        Service::create([
            'name' => 'Botox',
            'price' => 80,
            'duration' => 40
        ]);

        Service::create([
            'name' => 'Sobrancelha',
            'price' => 20,
            'duration' => 20
        ]);
    }
}