<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consola;
use App\Models\Tarifa;

class ConsolasTarifasSeeder extends Seeder
{
    public function run(): void
    {
        // Consolas
        $consolas = [
            ['nombre' => 'PS4', 'descripcion' => 'PlayStation 4', 'disponible' => true],
            ['nombre' => 'PS5', 'descripcion' => 'PlayStation 5', 'disponible' => true],
            ['nombre' => 'Xbox One', 'descripcion' => 'Xbox One', 'disponible' => true],
            ['nombre' => 'Xbox Series X', 'descripcion' => 'Xbox Series X', 'disponible' => true],
            ['nombre' => 'Xbox 360', 'descripcion' => 'Xbox 360', 'disponible' => true],
        ];

        foreach ($consolas as $c) {
            Consola::updateOrCreate(
                ['nombre' => $c['nombre']],
                ['descripcion' => $c['descripcion'], 'disponible' => $c['disponible']]
            );
        }

        // Tarifas por consola (guardamos con description para mapear fácilmente)
        $tarifas = [
            ['precio_hora' => 3500, 'descripcion' => 'Tarifa PS4 / Xbox One'],
            ['precio_hora' => 5000, 'descripcion' => 'Tarifa PS5'],
            ['precio_hora' => 4000, 'descripcion' => 'Tarifa Xbox Series X'],
            ['precio_hora' => 3000, 'descripcion' => 'Tarifa Xbox 360'],
        ];

        foreach ($tarifas as $t) {
            Tarifa::updateOrCreate(
                ['descripcion' => $t['descripcion']],
                ['precio_hora' => $t['precio_hora']]
            );
        }
    }
}
