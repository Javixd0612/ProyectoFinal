<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Consola;

class ConsoleSeeder extends Seeder
{
    public function run()
    {
        // Consolas que quieres conservar (seed final)
        $consolas = [
            ['nombre' => 'Play 5', 'precio_hora' => 5000, 'descripcion' => 'PlayStation 5 en excelente estado'],
            ['nombre' => 'Play 4', 'precio_hora' => 4500, 'descripcion' => 'PlayStation 4 con mando original'],
            ['nombre' => 'Play 3', 'precio_hora' => 4000, 'descripcion' => 'PlayStation 3 retro con buenos juegos'],
            ['nombre' => 'Xbox 360', 'precio_hora' => 3000, 'descripcion' => 'Xbox 360 clásica con juegos populares'],
        ];

        // Nombres y precios que queremos eliminar (sensibles a mayúsculas -> usamos LOWER para comparación)
        $toRemoveNames = ['ps5 slimer', 'xbox series', 'nintendo'];
        $toRemovePrices = [13000, 7000, 6000];

        // Eliminamos por nombre (case-insensitive)
        $lowerNames = array_map('strtolower', $toRemoveNames);
        Consola::whereIn(DB::raw('LOWER(nombre)'), $lowerNames)->delete();

        // Eliminamos por precio (en caso de que el nombre no coincida exactamente)
        Consola::whereIn('precio_hora', $toRemovePrices)->delete();

        // Ahora seed normal: updateOrCreate para las que queremos tener
        foreach ($consolas as $c) {
            Consola::updateOrCreate(
                ['nombre' => $c['nombre']],
                $c
            );
        }
    }
}
