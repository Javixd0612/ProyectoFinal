<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'ejemplodeprueba@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('ejemplodeprueba123'),
            ]
        );

        // Si existe columna 'role', la seteamos
        if (Schema::hasColumn('users', 'role')) {
            if ($user->role !== 'admin') {
                $user->role = 'admin';
                $user->save();
            }
        }

        // Nota: si más adelante instalás Spatie podrías agregar assignRole() aquí
    }
}
