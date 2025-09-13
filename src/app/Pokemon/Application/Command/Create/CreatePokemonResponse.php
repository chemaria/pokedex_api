<?php

namespace App\Pokemon\Application\Command\Create;

use App\Pokemon\Application\View\PokemonView;

/**
 * Response object for a created Pokemon.
 */
final readonly class CreatePokemonResponse
{
    public function __construct(
        public PokemonView $pokemon
    ) {
    }

    public static function fromView(PokemonView $view): self
    {
        return new self($view);
    }
}