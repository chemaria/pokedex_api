<?php

namespace App\Pokemon\Application\Query\List;

use App\Pokemon\Application\View\PokemonListView;
use App\Pokemon\Domain\Repository\PokemonRepository;

final readonly class ListPokemonHandler
{
    public function __construct(
        private PokemonRepository $repository
    ) {
    }

    public function handle(ListPokemonQuery $query): PokemonListView
    {
        $pokemon = $this->repository->findAll();
        
        return PokemonListView::fromPokemonArray($pokemon);
    }
}