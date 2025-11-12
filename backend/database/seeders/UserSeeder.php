<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cliente
        User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => Hash::make('password'),
            'role' => 'cliente',
            'cpf' => '12345678901',
            'phone' => '11999999999',
        ]);

        // Atendente
        User::create([
            'name' => 'Atendente Teste',
            'email' => 'atendente@teste.com',
            'password' => Hash::make('password'),
            'role' => 'atendente',
        ]);

        // Administrador
        User::create([
            'name' => 'Admin Teste',
            'email' => 'admin@teste.com',
            'password' => Hash::make('password'),
            'role' => 'administrador',
        ]);
    }
}
