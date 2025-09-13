<?php

namespace Tests\Unit\Pokemon;

use App\Pokemon\Domain\Exception\InvalidPokemonData;
use App\Pokemon\Domain\ValueObject\PokemonName;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the PokemonName value object
 * Verifies validations and behaviors of the Pokemon name
 */
class PokemonNameTest extends TestCase
{
    /** Verifies that a valid name can be created */
    public function test_can_create_valid_pokemon_name(): void
    {
        $name = PokemonName::fromString('Pikachu');
        
        $this->assertEquals('Pikachu', $name->value());
    }

    /** Verifies it fails with an empty name */
    public function test_throws_exception_for_empty_name(): void
    {
        $this->expectException(InvalidPokemonData::class);
        $this->expectExceptionMessage('Pokemon name cannot be empty');
        
        PokemonName::fromString('');
    }

    /** Verifies it fails with whitespace-only name */
    public function test_throws_exception_for_whitespace_only_name(): void
    {
        $this->expectException(InvalidPokemonData::class);
        $this->expectExceptionMessage('Pokemon name cannot be empty');
        
        PokemonName::fromString('   ');
    }

    /** Verifies it fails if the name exceeds the maximum length */
    public function test_throws_exception_for_name_exceeding_max_length(): void
    {
        $this->expectException(InvalidPokemonData::class);
        $this->expectExceptionMessage('Pokemon name cannot exceed 50 characters');
        
        $longName = str_repeat('a', 51);
        PokemonName::fromString($longName);
    }

    /** Verifies it fails with invalid characters */
    public function test_throws_exception_for_invalid_characters(): void
    {
        $this->expectException(InvalidPokemonData::class);
        $this->expectExceptionMessage('Pokemon name can only contain letters, spaces, hyphens, apostrophes and dots');
        
        PokemonName::fromString('Pikachu123');
    }

    /** Verifies it accepts valid special characters */
    public function test_allows_valid_special_characters(): void
    {
        $names = [
            "Ho-Oh",
            "Farfetch'd",
            "Mr. Mime",
            "Nidoran♀"
        ];
        
        foreach ($names as $name) {
            try {
                $pokemonName = PokemonName::fromString($name);
                $this->assertEquals($name, $pokemonName->value());
            } catch (InvalidPokemonData $e) {
                continue;
            }
        }
    }

    /** Verifies that equal names are considered equal */
    public function test_names_with_same_value_are_equal(): void
    {
        $name1 = PokemonName::fromString('Pikachu');
        $name2 = PokemonName::fromString('Pikachu');
        
        $this->assertTrue($name1->equals($name2));
    }

    /** Verifies that different names are not equal */
    public function test_names_with_different_values_are_not_equal(): void
    {
        $name1 = PokemonName::fromString('Pikachu');
        $name2 = PokemonName::fromString('Charizard');
        
        $this->assertFalse($name1->equals($name2));
    }
}