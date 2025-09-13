<?php

namespace Tests\Feature\Pokemon;

use App\Models\PokemonModel;
use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonId;
use App\Pokemon\Domain\ValueObject\PokemonName;
use App\Pokemon\Infrastructure\Persistence\Eloquent\EloquentPokemonRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Integration tests for the Eloquent Pokemon repository
 * Verifies persistence and database queries
 */
class EloquentPokemonRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentPokemonRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentPokemonRepository();
    }

    /** Verifies that a new Pokemon can be saved to the database */
    public function test_can_save_new_pokemon(): void
    {
    $pokemonId = PokemonId::generate(); // null ID for new pokemon
        $pokemon = Pokemon::create(
            $pokemonId,
            PokemonName::fromString('Pikachu'),
            PokemonType::ELECTRIC,
            PokemonHp::fromInt(35),
            CaptureStatus::WILD
        );

        $savedPokemon = $this->repository->save($pokemon);

        $this->assertDatabaseHas('pokemon', [
            'name' => 'Pikachu',
            'type' => 'Electric',
            'hp' => 35,
            'status' => 'wild'
        ]);
        
    // Verify that the saved pokemon was returned with an assigned ID
        $this->assertNotNull($savedPokemon->id()->value());
        $this->assertIsInt($savedPokemon->id()->value());
        $this->assertGreaterThan(0, $savedPokemon->id()->value());
        $this->assertEquals('Pikachu', $savedPokemon->name()->value());
    }

    /** Verifies that a Pokemon can be found by its ID */
    public function test_can_find_pokemon_by_id(): void
    {
        $model = PokemonModel::create([
            'name' => 'Charizard',
            'type' => 'Fire',
            'hp' => 78,
            'status' => 'captured'
        ]);

        $pokemon = $this->repository->findById(PokemonId::fromInt($model->id));

        $this->assertNotNull($pokemon);
        $this->assertEquals($model->id, $pokemon->id()->value());
        $this->assertEquals('Charizard', $pokemon->name()->value());
        $this->assertEquals(PokemonType::FIRE, $pokemon->type());
        $this->assertEquals(78, $pokemon->hp()->value());
        $this->assertEquals(CaptureStatus::CAPTURED, $pokemon->status());
    }

    /** Verifies it returns null when the Pokemon is not found */
    public function test_returns_null_when_pokemon_not_found(): void
    {
        $pokemon = $this->repository->findById(PokemonId::fromInt(999));

        $this->assertNull($pokemon);
    }

    /** Verifies that all Pokemon can be retrieved */
    public function test_can_find_all_pokemon(): void
    {
        PokemonModel::create([
            'name' => 'Pikachu',
            'type' => 'Electric',
            'hp' => 35,
            'status' => 'wild'
        ]);

        PokemonModel::create([
            'name' => 'Charizard',
            'type' => 'Fire',
            'hp' => 78,
            'status' => 'captured'
        ]);

        $pokemon = $this->repository->findAll();

        $this->assertCount(2, $pokemon);
        $this->assertEquals('Pikachu', $pokemon[0]->name()->value());
        $this->assertEquals('Charizard', $pokemon[1]->name()->value());
    }

    /** Verifies that existence of a Pokemon can be checked */
    public function test_can_check_if_pokemon_exists(): void
    {
        $model = PokemonModel::create([
            'name' => 'Pikachu',
            'type' => 'Electric',
            'hp' => 35,
            'status' => 'wild'
        ]);

        $this->assertTrue($this->repository->exists(PokemonId::fromInt($model->id)));
        $this->assertFalse($this->repository->exists(PokemonId::fromInt(999)));
    }
}
