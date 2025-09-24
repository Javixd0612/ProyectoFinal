<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consola;

class ConsoleSeeder extends Seeder
{
    public function run()
    {
        $consolas = [
            ['nombre' => 'PS5 Slim', 'precio_hora' => 12000, 'descripcion' => 'PlayStation 5 Slim - buen estado'],
            ['nombre' => 'Xbox Series S', 'precio_hora' => 9000, 'descripcion' => 'Xbox Series S compacta'],
            ['nombre' => 'Nintendo Switch', 'precio_hora' => 7000, 'descripcion' => 'Switch con dock'],
        ];

        foreach ($consolas as $c) {
            Consola::updateOrCreate(['nombre' => $c['nombre']], $c);
        }
    }
}
