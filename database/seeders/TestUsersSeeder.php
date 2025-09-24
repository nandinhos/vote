<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin de teste
        User::firstOrCreate(
            ['saram' => '1234567'],
            [
                'name' => 'Admin Teste',
                'saram' => '1234567',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Criar usuário voter de teste
        User::firstOrCreate(
            ['saram' => '7654321'],
            [
                'name' => 'Eleitor Teste',
                'saram' => '7654321',
                'password' => Hash::make('password'),
                'role' => 'voter',
            ]
        );

        // Criar alguns usuários voters adicionais para teste
        User::firstOrCreate(
            ['saram' => '1111111'],
            [
                'name' => 'João Silva',
                'saram' => '1111111',
                'password' => Hash::make('password'),
                'role' => 'voter',
            ]
        );

        User::firstOrCreate(
            ['saram' => '2222222'],
            [
                'name' => 'Maria Santos',
                'saram' => '2222222',
                'password' => Hash::make('password'),
                'role' => 'voter',
            ]
        );

        User::firstOrCreate(
            ['saram' => '3333333'],
            [
                'name' => 'Pedro Costa',
                'saram' => '3333333',
                'password' => Hash::make('password'),
                'role' => 'voter',
            ]
        );

        User::firstOrCreate(
            ['saram' => '4112695'],
            [
                'name' => '1S BMB FERNANDO',
                'saram' => '4112695',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}