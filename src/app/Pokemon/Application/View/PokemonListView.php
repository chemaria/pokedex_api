<?php

namespace App\Pokemon\Application\View;

use App\Pokemon\Domain\Entity\Pokemon;

/**
 * Represents a presentation-friendly list of Pokémon for the application layer.
 *
 * This view aggregates multiple PokemonView objects and exposes the total number
 * of items. It is intended to be created from domain models (via fromPokemonArray)
 * and converted to a simple array structure suitable for JSON serialization
 * (via toArray).
 *
 * Immutable/read-only: once constructed the contained data and total cannot be changed.
 */
 
/**
 * Create a new PokemonListView.
 *
 * @param PokemonView[] $pokemon Array of PokemonView objects representing each Pokémon in the list.
 * @param int           $total   Total number of Pokémon in the list (typically count of $pokemon).
 */
 
/**
 * Convert an array of domain Pokemon models into a PokemonListView.
 *
 * Each domain Pokemon is mapped to a PokemonView using PokemonView::fromDomain().
 *
 * @param Pokemon[] $pokemon Array of domain Pokemon models to convert.
 * @return self
 */
 
/**
 * Return the view as a plain PHP array suitable for JSON encoding.
 *
 * The returned array has the shape:
 * [
 *     'data'  => array<int, array> , // list of PokemonView->toArray() results
 *     'total' => int                 // total number of items
 * ]
 *
 * @return array{data: array<int, array<string, mixed>>, total: int}
 */
final readonly class PokemonListView
{
    public function __construct(
        public array $pokemon,
        public int $total
    ) {
    }

    public static function fromPokemonArray(array $pokemon): self
    {
        $pokemonViews = array_map(
            fn(Pokemon $pokemon) => PokemonView::fromDomain($pokemon),
            $pokemon
        );

        return new self(
            pokemon: $pokemonViews,
            total: count($pokemonViews)
        );
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn(PokemonView $view) => $view->toArray(), $this->pokemon),
            'total' => $this->total
        ];
    }
}