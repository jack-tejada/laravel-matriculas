<?php

namespace Database\Seeders;

use App\Models\Matricula;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar matrículas para que no haya datos falsos de relleno
        Matricula::truncate();

        // Usuarios base con nombres limpios y dominio @mail.com
        User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'editor@mail.com'],
            [
                'name' => 'editor',
                'password' => Hash::make('123456'),
                'role' => 'editor',
            ]
        );

        User::updateOrCreate(
            ['email' => 'lector@mail.com'],
            [
                'name' => 'lector',
                'password' => Hash::make('123456'),
                'role' => 'lector',
            ]
        );
    }
}
