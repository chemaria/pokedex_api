<?php

namespace App\Pokemon\Domain\Event;

use App\Pokemon\Domain\ValueObject\PokemonId;
use App\Pokemon\Domain\ValueObject\PokemonName;
use DateTimeImmutable;

/**
 * Domain event raised when a Pokémon is captured.
 *
 * Represents an immutable, transportable record that a capture has occurred
 * in the domain. Instances of this event carry the information required by
 * subscribers (read model updaters, notification handlers, audit loggers, etc.)
 * to react to the capture.
 *
 * Typical payload fields (implementation-specific):
 * - pokemonId   : identifier of the captured Pokémon
 * - trainerId   : identifier of the trainer who captured the Pokémon
 * - capturedAt  : timestamp of when the capture happened
 * - location    : optional metadata describing where the capture occurred
 *
 * The class is readonly/immutable and intended to be safe for publishing on
 * event buses and persisting in an event store.
 *
 * @see \App\Pokemon\Domain\Event
 */
final readonly class PokemonCaptured
{
    public function __construct(
        private PokemonId $pokemonId,
        private PokemonName $pokemonName,
        private DateTimeImmutable $occurredAt = new DateTimeImmutable()
    ) {
    }

    public function pokemonId(): PokemonId
    {
        return $this->pokemonId;
    }

    public function pokemonName(): PokemonName
    {
        return $this->pokemonName;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function toArray(): array
    {
        return [
            'pokemon_id' => $this->pokemonId->value(),
            'pokemon_name' => $this->pokemonName->value(),
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s')
        ];
    }
}
