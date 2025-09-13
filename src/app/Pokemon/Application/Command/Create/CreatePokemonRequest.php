<?php

namespace App\Pokemon\Application\Command\Create;

/**
 * Request object for creating a new Pokemon.
 */
final readonly class CreatePokemonRequest
{
    public function __construct(
        public string $name,
        public string $type,
        public int $hp,
        public string $status = 'wild'
    ) {
    }
}