<?php

namespace App\Pokemon\Domain\ValueObject;

use App\Pokemon\Domain\Exception\InvalidPokemonData;


/**
 * Represents the name of a Pokemon.
 * Validates that the name is non-empty, within length limits, and contains only valid characters.
 */
final readonly class PokemonName
{
    public function __construct(private string $value)
    {
        $this->validate($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(PokemonName $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(string $value): void
    {
        $trimmedValue = trim($value);
        
        if (empty($trimmedValue)) {
            throw new InvalidPokemonData('Pokemon name cannot be empty');
        }

        if (strlen($trimmedValue) > 50) {
            throw new InvalidPokemonData('Pokemon name cannot exceed 50 characters');
        }

        if (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $trimmedValue)) {
            throw new InvalidPokemonData('Pokemon name can only contain letters, spaces, hyphens, apostrophes and dots');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}