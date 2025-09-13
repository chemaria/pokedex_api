<?php

namespace App\Pokemon\Application\Query\GetById;

final readonly class GetPokemonByIdQuery
{
    public function __construct(
        public string $id
    ) {
    }
}
