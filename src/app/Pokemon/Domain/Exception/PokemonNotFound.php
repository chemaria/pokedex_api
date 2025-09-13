<?php

namespace App\Pokemon\Domain\Exception;

use App\Pokemon\Domain\ValueObject\PokemonId;
use DomainException;

class PokemonNotFound extends DomainException
{
    public static function withId(PokemonId $id): self
    {
        return new self(sprintf('Pokemon with ID "%s" not found', $id->value()));
    }
    //TODO
    public static function withName(string $name): self
    {
        return new self(sprintf('Pokemon with name "%s" not found', $name));
    }
}
