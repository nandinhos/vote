<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // KC-390
        Project::firstOrCreate(
            ['name' => 'KC-390'],
            [
                'name' => 'KC-390',
                'description' => 'Aeronave de Transporte Tático/Logístico',
                'is_active' => true,
            ]
        );

        // A-1M
        Project::firstOrCreate(
            ['name' => 'A-1M'],
            [
                'name' => 'A-1M',
                'description' => 'Aeronave de Caça-Bombardeiro',
                'is_active' => true,
            ]
        );

        // F-5M
        Project::firstOrCreate(
            ['name' => 'F-5M'],
            [
                'name' => 'F-5M',
                'description' => 'Aeronave de Caça',
                'is_active' => true,
            ]
        );

        // E-99M
        Project::firstOrCreate(
            ['name' => 'E-99M'],
            [
                'name' => 'E-99M',
                'description' => 'Aeronave de Alerta Aéreo e Controle (AEW&C)',
                'is_active' => true,
            ]
        );

        // FX-2
        Project::firstOrCreate(
            ['name' => 'FX-2'],
            [
                'name' => 'FX-2',
                'description' => 'Aeronave de Caça',
                'is_active' => true,
            ]
        );

        // H-36
        Project::firstOrCreate(
            ['name' => 'H-36'],
            [
                'name' => 'H-36',
                'description' => 'Helicóptero Caracal (Multimissão)',
                'is_active' => true,
            ]
        );

        // H-125
        Project::firstOrCreate(
            ['name' => 'H-125'],
            [
                'name' => 'H-125',
                'description' => 'Helicóptero Esquilo',
                'is_active' => true,
            ]
        );

        // A-29
        Project::firstOrCreate(
            ['name' => 'A-29'],
            [
                'name' => 'A-29',
                'description' => 'Aeronave de Ataque Leve / Treinamento Avançado',
                'is_active' => true,
            ]
        );

        // I-X
        Project::firstOrCreate(
            ['name' => 'I-X'],
            [
                'name' => 'I-X',
                'description' => 'Aeronave de Inspeção em Voo',
                'is_active' => true,
            ]
        );

        // C-95
        Project::firstOrCreate(
            ['name' => 'C-95'],
            [
                'name' => 'C-95',
                'description' => 'Aeronave de Transporte Tático/Logístico',
                'is_active' => true,
            ]
        );
    }
}
