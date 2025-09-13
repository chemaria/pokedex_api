<?php

namespace Tests\Unit\Pokemon;

use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\Event\PokemonCaptured;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonId;
use App\Pokemon\Domain\ValueObject\PokemonName;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Pokemon entity
 * Verifies behaviors of the Pokemon domain
 */
class PokemonTest extends TestCase
{
    private Pokemon $pokemon;
    private PokemonId $pokemonId;

    protected function setUp(): void
    {
        $this->pokemonId = PokemonId::fromInt(1);
        $this->pokemon = Pokemon::create(
            $this->pokemonId,
            PokemonName::fromString('Pikachu'),
            PokemonType::ELECTRIC,
            PokemonHp::fromInt(35),
            CaptureStatus::WILD
        );
    }

    /** Verifies that a Pokemon can be created with all its attributes */
    public function test_can_create_pokemon(): void
    {
        $this->assertEquals($this->pokemonId->value(), $this->pokemon->id()->value());
        $this->assertEquals('Pikachu', $this->pokemon->name()->value());
        $this->assertEquals(PokemonType::ELECTRIC, $this->pokemon->type());
        $this->assertEquals(35, $this->pokemon->hp()->value());
        $this->assertEquals(CaptureStatus::WILD, $this->pokemon->status());
    }

    /** Verifies that a wild Pokemon can be captured and generates events */
    public function test_can_capture_wild_pokemon(): void
    {
        $this->pokemon->capture();

        $this->assertEquals(CaptureStatus::CAPTURED, $this->pokemon->status());

        $events = $this->pokemon->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(PokemonCaptured::class, $events[0]);
    }

    /** Verifies Pokemon equality based on its ID */
    public function test_pokemon_equality(): void
    {
        $samePokemon = Pokemon::create(
            $this->pokemonId,
            PokemonName::fromString('Different Name'),
            PokemonType::FIRE,
            PokemonHp::fromInt(100),
            CaptureStatus::WILD
        );

        $differentPokemon = Pokemon::create(
            PokemonId::fromInt(2), 
            PokemonName::fromString('Pikachu'),
            PokemonType::ELECTRIC,
            PokemonHp::fromInt(35),
            CaptureStatus::WILD
        );

        $this->assertTrue($this->pokemon->equals($samePokemon));
        $this->assertFalse($this->pokemon->equals($differentPokemon));
    }

    /** Verifies that domain events are cleared after being pulled */
    public function test_pulling_domain_events_clears_events(): void
    {
        $this->pokemon->capture();

        $events = $this->pokemon->pullDomainEvents();
        $this->assertCount(1, $events);

        $eventsAgain = $this->pokemon->pullDomainEvents();
        $this->assertCount(0, $eventsAgain);
    }
}
