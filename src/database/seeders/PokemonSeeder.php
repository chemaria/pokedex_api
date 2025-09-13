<?php

namespace Database\Seeders;

use App\Models\PokemonModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PokemonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos Pokemon de ejemplo específicos
        $pokemonData = [
            [
                'name' => 'Pikachu',
                'type' => 'Electric',
                'hp' => 35,
                'status' => 'captured'
            ],
            [
                'name' => 'Charizard',
                'type' => 'Fire',
                'hp' => 78,
                'status' => 'captured'
            ],
            [
                'name' => 'Blastoise',
                'type' => 'Water',
                'hp' => 79,
                'status' => 'wild'
            ],
            [
                'name' => 'Venusaur',
                'type' => 'Grass',
                'hp' => 80,
                'status' => 'wild'
            ]
        ];

        foreach ($pokemonData as $pokemon) {
            PokemonModel::create($pokemon);
        }

        // Crear Pokemon adicionales usando el factory
        PokemonModel::factory()->count(6)->create();
    }
}
