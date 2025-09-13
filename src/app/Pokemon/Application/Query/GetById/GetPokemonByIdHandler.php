<?php

namespace App\Pokemon\Application\Query\GetById;

use App\Pokemon\Application\View\PokemonView;
use App\Pokemon\Domain\Exception\PokemonNotFound;
use App\Pokemon\Domain\Repository\PokemonRepository;
use App\Pokemon\Domain\ValueObject\PokemonId;

final readonly class GetPokemonByIdHandler
{
    public function __construct(
        private PokemonRepository $repository
    ) {
    }

    public function handle(GetPokemonByIdQuery $query): PokemonView
    {
        $pokemonId = PokemonId::fromString($query->id);
        $pokemon = $this->repository->findById($pokemonId);

        if ($pokemon === null) {
            throw PokemonNotFound::withId($pokemonId);
        }

        return PokemonView::fromDomain($pokemon);
    }
}
