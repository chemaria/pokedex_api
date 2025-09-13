<?php

namespace App\Pokemon\Application\View;

use App\Pokemon\Domain\Entity\Pokemon;

/**
 * PokemonView
 *
 * Immutable view/DTO representing a Pokemon for the application (presentation) layer.
 * Intended to be created from a domain Pokemon object and used for serialisation (e.g. JSON responses).
 *
 * Properties:
 *  @property-read ?int    $id     Unique identifier. Nullable when the domain entity is not yet persisted.
 *  @property-read string  $name   Pokemon's name.
 *  @property-read string  $type   Pokemon's type (e.g. "Fire", "Water").
 *  @property-read int     $hp     Current hit points.
 *  @property-read string  $status Current status or condition (e.g. "Healthy", "Fainted").
 *
 * Usage:
 *  - Use PokemonView::fromDomain($pokemon) to construct an instance from a domain Pokemon aggregate.
 *  - Use $view->toArray() to obtain an associative array suitable for HTTP responses or templates.
 *
 * Behavioural notes:
 *  - The class is final and readonly by design to guarantee immutability and prevent extension.
 *  - All values are simple scalars suitable for transport; any domain-specific value objects are unwrapped.
 *
 * Methods:
 *  @method static self fromDomain(Pokemon $pokemon) Create a PokemonView from a domain Pokemon object.
 *  @method array toArray() Convert the view into an associative array with keys: id, name, type, hp, status.
 */
final readonly class PokemonView
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $type,
        public int $hp,
        public string $status
    ) {
    }
    public static function fromDomain(Pokemon $pokemon): self
    {
        return new self(
            id: $pokemon->id()->value(),
            name: $pokemon->name()->value(),
            type: $pokemon->type()->value,
            hp: $pokemon->hp()->value(),
            status: $pokemon->status()->value
        );
    }
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'hp' => $this->hp,
            'status' => $this->status
        ];
    }
}
