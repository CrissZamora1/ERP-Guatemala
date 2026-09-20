<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sucursales = [
            ['nombre' => 'Sucursal 1', 'codigo' => 'SUC01'],
            ['nombre' => 'Sucursal 2', 'codigo' => 'SUC02'],
            ['nombre' => 'Sucursal 3', 'codigo' => 'SUC03'],
            ['nombre' => 'Sucursal 4', 'codigo' => 'SUC04'],
        ];

        foreach ($sucursales as $sucursal) {
            \App\Models\Sucursal::create($sucursal);
        }
    }
}
