<?php

namespace App\Pokemon\Infrastructure\Http\Resource;

use App\Pokemon\Application\View\PokemonView;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a PokemonView into a JSON-serializable format for API responses.
 */
class PokemonResource extends JsonResource
{
    public function __construct(private PokemonView $pokemonView)
    {
        parent::__construct($pokemonView);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pokemonView->id,
            'name' => $this->pokemonView->name,
            'type' => $this->pokemonView->type,
            'hp' => $this->pokemonView->hp,
            'status' => $this->pokemonView->status
        ];
    }

    public static function collection($resource): AnonymousResourceCollection
    {
        if (is_array($resource)) {
            $resource = collect($resource);
        }

        return parent::collection($resource->map(fn(PokemonView $view) => new self($view)));
    }
}
