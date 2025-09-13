<?php

namespace Tests\Feature\Pokemon;

use App\Models\PokemonModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Integration tests for the Pokemon REST API
 * Verifies HTTP endpoints and JSON responses
 */
class PokemonApiTest extends TestCase
{
    use RefreshDatabase;

    /** Verifies that all Pokemon can be listed */
    public function test_can_list_all_pokemon(): void
    {
        // Arrange: Create some test data
        PokemonModel::factory()->create([
            'name' => 'Pikachu',
            'type' => 'Electric',
            'hp' => 35,
            'status' => 'captured'
        ]);

        PokemonModel::factory()->create([
            'name' => 'Charizard',
            'type' => 'Fire',
            'hp' => 78,
            'status' => 'wild'
        ]);

        // Act
        $response = $this->getJson('/api/pokemon');

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'type',
                        'hp',
                        'status'
                    ]
                ],
                'total'
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('total', 2);
    }

    /** Verifies that a Pokemon can be retrieved by its ID */
    public function test_can_get_pokemon_by_id(): void
    {
        // Arrange
        $pokemon = PokemonModel::factory()->create([
            'name' => 'Blastoise',
            'type' => 'Water',
            'hp' => 79,
            'status' => 'captured'
        ]);

        // Act
        $response = $this->getJson("/api/pokemon/{$pokemon->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'type',
                    'hp',
                    'status'
                ]
            ])
            ->assertJsonPath('data.name', 'Blastoise')
            ->assertJsonPath('data.type', 'Water')
            ->assertJsonPath('data.hp', 79)
            ->assertJsonPath('data.status', 'captured');
    }

    /** Verifies it returns 404 when the Pokemon is not found */
    public function test_returns_404_when_pokemon_not_found(): void
    {
        $nonExistentId = 999;
        $response = $this->getJson("/api/pokemon/{$nonExistentId}");

        // Assert
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'error',
                'message'
            ])
            ->assertJsonPath('error', 'Pokemon not found');
    }

    /** Verifies that a new Pokemon can be created */
    public function test_can_create_new_pokemon(): void
    {
        // Arrange
        $pokemonData = [
            'name' => 'Venusaur',
            'type' => 'Grass',
            'hp' => 80,
            'status' => 'wild'
        ];

        // Act
        $response = $this->postJson('/api/pokemon', $pokemonData);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'type',
                    'hp',
                    'status'
                ]
            ])
            ->assertJsonPath('data.name', 'Venusaur')
            ->assertJsonPath('data.type', 'Grass')
            ->assertJsonPath('data.hp', 80)
            ->assertJsonPath('data.status', 'wild');

        // Verify in database
        $this->assertDatabaseHas('pokemon', [
            'name' => 'Venusaur',
            'type' => 'Grass',
            'hp' => 80,
            'status' => 'wild'
        ]);
    }

    /** Verifies that the default status is 'wild' */
    public function test_create_pokemon_with_default_status(): void
    {
        // Arrange
        $pokemonData = [
            'name' => 'Jigglypuff',
            'type' => 'Normal',
            'hp' => 55
            // No status provided, should default to 'wild'
        ];

        // Act
        $response = $this->postJson('/api/pokemon', $pokemonData);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.status', 'wild');
    }

    /** Verifies validation errors when creating a Pokemon */
    public function test_create_pokemon_validation_errors(): void
    {
        $testCases = [
            [
                'data' => [],
                'errors' => ['name', 'type', 'hp']
            ],
            [
                'data' => [
                    'name' => '',
                    'type' => 'Electric',
                    'hp' => 35
                ],
                'errors' => ['name']
            ],
            [
                'data' => [
                    'name' => 'Pikachu',
                    'type' => 'InvalidType',
                    'hp' => 35
                ],
                'errors' => ['type']
            ],
            [
                'data' => [
                    'name' => 'Pikachu',
                    'type' => 'Electric',
                    'hp' => 0
                ],
                'errors' => ['hp']
            ],
            [
                'data' => [
                    'name' => 'Pikachu',
                    'type' => 'Electric',
                    'hp' => 101
                ],
                'errors' => ['hp']
            ],
            [
                'data' => [
                    'name' => str_repeat('a', 51),
                    'type' => 'Electric',
                    'hp' => 35
                ],
                'errors' => ['name']
            ]
        ];

        foreach ($testCases as $testCase) {
            $response = $this->postJson('/api/pokemon', $testCase['data']);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            foreach ($testCase['errors'] as $field) {
                $response->assertJsonValidationErrors($field);
            }
        }
    }

    /** Verifies that Pokemon cannot be created with duplicate names */
    public function test_cannot_create_duplicate_pokemon_name(): void
    {
        PokemonModel::factory()->create(['name' => 'Pikachu']);

        $pokemonData = [
            'name' => 'Pikachu',
            'type' => 'Electric',
            'hp' => 35
        ];

        // Act
        $response = $this->postJson('/api/pokemon', $pokemonData);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    /** Verifies that valid special characters are accepted in names */
    public function test_pokemon_name_accepts_valid_special_characters(): void
    {
        $validNames = [
            'Ho-Oh',
            "Farfetch'd",
            'Mr. Mime'
        ];

        foreach ($validNames as $name) {
            $pokemonData = [
                'name' => $name,
                'type' => 'Normal',
                'hp' => 50
            ];

            $response = $this->postJson('/api/pokemon', $pokemonData);

            $response->assertStatus(Response::HTTP_CREATED);
        }
    }

    /** Verifies it returns an empty array when there are no Pokemon */
    public function test_list_pokemon_returns_empty_array_when_no_pokemon(): void
    {
        // Act
        $response = $this->getJson('/api/pokemon');

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', [])
            ->assertJsonPath('total', 0);
    }
}
