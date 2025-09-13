<?php

namespace App\Pokemon\Domain\ValueObject;

use App\Pokemon\Domain\Exception\InvalidPokemonData;

/**
 * Value Object para el ID de un Pokémon
 * Permite IDs nulos (para creación) y valida que sean positivos
 */
final readonly class PokemonId
{
    public function __construct(private ?int $value)
    {
        $this->validate($value);
    }

    public static function fromInt(?int $value): self
    {
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        if (empty(trim($value))) {
            throw new InvalidPokemonData('Pokemon ID cannot be empty');
        }
        
        if (!is_numeric($value)) {
            throw new InvalidPokemonData('Pokemon ID must be a valid integer');
        }

        return new self((int) $value);
    }

    public static function generate(): self
    {
        // Para IDs auto-incrementales, retornamos null
        // El repositorio se encargará de asignar el ID
        // En una app real suelo generar UUIDs
        return new self(null);
    }

    public function value(): ?int
    {
        return $this->value;
    }

    public function equals(PokemonId $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(?int $value): void
    {
        if ($value !== null && $value <= 0) {
            throw new InvalidPokemonData('Pokemon ID must be a positive integer');
        }
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
