<?php

namespace Tests\Unit\Pokemon;

use App\Models\PokemonModel;
use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonId;
use App\Pokemon\Domain\ValueObject\PokemonName;
use App\Pokemon\Infrastructure\Persistence\Eloquent\PokemonMapper;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the mapper between Pokemon domain entity and Eloquent model
 * Verifies bidirectional conversions between domain and infrastructure
 */
class PokemonMapperTest extends TestCase
{
    /** Verifies mapping from domain entity to Eloquent array */
    public function test_can_map_domain_entity_to_eloquent_array(): void
    {
        $pokemon = Pokemon::create(
            PokemonId::fromInt(1),
            PokemonName::fromString('Pikachu'),
            PokemonType::ELECTRIC,
            PokemonHp::fromInt(35),
            CaptureStatus::CAPTURED
        );

        $eloquentData = PokemonMapper::toEloquent($pokemon);

        $expected = [
            'id' => 1,
            'name' => 'Pikachu',
            'type' => 'Electric',
            'hp' => 35,
            'status' => 'captured'
        ];

        $this->assertEquals($expected, $eloquentData);
    }

    /** Verifies mapping when the Pokemon has no ID (new) */
    public function test_can_map_domain_entity_with_null_id_to_eloquent_array(): void
    {
        $pokemon = Pokemon::create(
            PokemonId::generate(), 
            PokemonName::fromString('Pikachu'),
            PokemonType::ELECTRIC,
            PokemonHp::fromInt(35),
            CaptureStatus::CAPTURED
        );

        $eloquentData = PokemonMapper::toEloquent($pokemon);

        $expected = [
            'name' => 'Pikachu',
            'type' => 'Electric',
            'hp' => 35,
            'status' => 'captured'
        ];

        $this->assertEquals($expected, $eloquentData);
    }

    /** Verifies mapping from Eloquent model to domain entity */
    public function test_can_map_eloquent_model_to_domain_entity(): void
    {
        $model = new PokemonModel();
        $model->id = 1;
        $model->name = 'Charizard';
        $model->type = 'Fire';
        $model->hp = 78;
        $model->status = 'wild';

        $pokemon = PokemonMapper::toDomain($model);

        $this->assertEquals(1, $pokemon->id()->value());
        $this->assertEquals('Charizard', $pokemon->name()->value());
        $this->assertEquals(PokemonType::FIRE, $pokemon->type());
        $this->assertEquals(78, $pokemon->hp()->value());
        $this->assertEquals(CaptureStatus::WILD, $pokemon->status());
    }
}
