<?php

namespace Database\Factories;

use App\Models\PokemonModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PokemonModel>
 */
class PokemonModelFactory extends Factory
{
    protected $model = PokemonModel::class;
    private static $pokemonNames = [
        'Pikachu', 'Charizard', 'Blastoise', 'Venusaur', 'Jigglypuff',
        'Mewtwo', 'Mew', 'Gyarados', 'Dragonite', 'Alakazam',
        'Machamp', 'Golem', 'Gengar', 'Onix', 'Hitmonlee',
        'Scyther', 'Electabuzz', 'Magmar', 'Lapras', 'Eevee'
    ];

    private static $types = [
        'Electric', 'Fire', 'Water', 'Grass', 'Rock', 'Flying',
        'Bug', 'Normal', 'Fighting', 'Poison', 'Ground',
        'Psychic', 'Ice', 'Dragon', 'Dark', 'Steel', 'Fairy'
    ];

    private static $statuses = ['wild', 'captured'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(self::$pokemonNames),
            'type' => $this->faker->randomElement(self::$types),
            'hp' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement(self::$statuses),
        ];
    }
}
