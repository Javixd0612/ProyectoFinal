<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Crear rol admin si no existe
        Role::firstOrCreate(['name' => 'admin']);

        // Crear o actualizar usuario admin
        $user = User::updateOrCreate(
            ['email' => 'ejemplodeprueba@gmail.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('ejemplodeprueba123'),
            ]
        );

        // Asignar rol admin
        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
