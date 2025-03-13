<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'name' => 'Rental PS 4',
            'description' => 'Paket rental PlayStation 4 selama 1 sesi',
            'price' => 30000,
        ]);

        Service::create([
            'name' => 'Rental PS 5',
            'description' => 'Paket rental PlayStation 5 selama 1 sesi',
            'price' => 40000,
        ]);
    }
}
