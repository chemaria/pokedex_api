<?php

namespace Tests\Unit\Pokemon;

use App\Pokemon\Domain\Exception\InvalidPokemonData;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the PokemonHp value object
 * Verifies validations and behaviors of hit points
 */
class PokemonHpTest extends TestCase
{
    /** Verifies that a valid HP can be created */
    public function test_can_create_valid_pokemon_hp(): void
    {
        $hp = PokemonHp::fromInt(50);

        $this->assertEquals(50, $hp->value());
    }

    /** Verifies it fails with HP below the minimum */
    public function test_throws_exception_for_hp_below_minimum(): void
    {
        $this->expectException(InvalidPokemonData::class);
        $this->expectExceptionMessage('Pokemon HP must be between 1 and 100, got 0');

        PokemonHp::fromInt(0);
    }

    /** Verifies it fails with HP above the maximum */
    public function test_throws_exception_for_hp_above_maximum(): void
    {
        $this->expectException(InvalidPokemonData::class);
        $this->expectExceptionMessage('Pokemon HP must be between 1 and 100, got 101');

        PokemonHp::fromInt(101);
    }

    /** Verifies it accepts the minimum HP (1) */
    public function test_can_create_minimum_hp(): void
    {
        $hp = PokemonHp::fromInt(1);

        $this->assertEquals(1, $hp->value());
    }

    /** Verifies it accepts the maximum HP (100) */
    public function test_can_create_maximum_hp(): void
    {
        $hp = PokemonHp::fromInt(100);

        $this->assertEquals(100, $hp->value());
    }

    /** Verifies that equal HP values are considered equal */
    public function test_hp_values_with_same_amount_are_equal(): void
    {
        $hp1 = PokemonHp::fromInt(50);
        $hp2 = PokemonHp::fromInt(50);

        $this->assertTrue($hp1->equals($hp2));
    }

    /** Verifies that different HP values are not equal */
    public function test_hp_values_with_different_amounts_are_not_equal(): void
    {
        $hp1 = PokemonHp::fromInt(50);
        $hp2 = PokemonHp::fromInt(60);

        $this->assertFalse($hp1->equals($hp2));
    }
}
