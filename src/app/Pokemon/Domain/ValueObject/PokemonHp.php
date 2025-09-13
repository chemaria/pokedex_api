<?php

namespace App\Pokemon\Domain\ValueObject;

use App\Pokemon\Domain\Exception\InvalidPokemonData;

/** 
 * Represents the HP (Hit Points) of a Pokemon.
 * Validates that HP is between 1 and 100 inclusive.
 */
final readonly class PokemonHp
{
    private const MIN_HP = 1;
    private const MAX_HP = 100;

    public function __construct(private int $value)
    {
        $this->validate($value);
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(PokemonHp $other): bool
    {
        return $this->value === $other->value;
    }


    private function validate(int $value): void
    {
        if ($value < self::MIN_HP || $value > self::MAX_HP) {
            throw new InvalidPokemonData(
                sprintf('Pokemon HP must be between %d and %d, got %d', self::MIN_HP, self::MAX_HP, $value)
            );
        }
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
