<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Admin
        User::create([
            'name' => 'admin1',
            'email' => 'admin@nexus.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'bio' => 'Administrador principal de Nexus Games. Apasionado por los videojuegos y la tecnología.',
            'email_verified_at' => now(),
        ]);

        // Usuario Normal
        User::create([
            'name' => 'Juanex',
            'email' => 'juan@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'bio' => 'Gamer desde la infancia. Me encantan los juegos de acción y aventura.',
            'email_verified_at' => now(),
        ]);

        // Usuarios adicionales para pruebas
        User::create([
            'name' => 'Oasis_119',
            'email' => 'maria@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'bio' => 'Fanática de los juegos de terror y supervivencia.',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Vegeta777',
            'email' => 'carlos@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'bio' => 'Estratega nato. Siempre buscando el próximo desafío.',
            'email_verified_at' => now(),
        ]);
    }
}