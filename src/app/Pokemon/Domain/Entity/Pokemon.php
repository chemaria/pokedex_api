<?php

namespace App\Pokemon\Domain\Entity;

use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\Event\PokemonCaptured;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonId;
use App\Pokemon\Domain\ValueObject\PokemonName;

/**
 * Domain entity representing a Pokémon.
 *
 * Encapsulates identity, attributes and domain behaviour for a Pokémon in the application:
 * - Holds value objects for id, name, type and hp, and a CaptureStatus.
 * - Exposes behavior to capture a Pokémon and to publish domain events produced by the entity.
 * - Internally accumulates domain events in $domainEvents; they can be retrieved and cleared via pullDomainEvents().
 *
 * Behaviour summary:
 * - create(...): Static factory that constructs a new Pokémon instance. When using the factory, the capture
 *   status defaults to CaptureStatus::WILD if not provided.
 * - capture(): If the Pokémon is not already captured, transitions its CaptureStatus to a captured state and
 *   records a PokemonCaptured domain event with the Pokémon id and name.
 * - pullDomainEvents(): Returns an array of recorded domain events and clears the internal events list.
 * - equals(Pokemon $other): Compares two Pokémon entities by identity (their PokemonId).
 *
 * Constructor parameters:
 * @param PokemonId     $id     Unique identifier for the Pokémon.
 * @param PokemonName   $name   Name value object.
 * @param PokemonType   $type   Type value object.
 * @param PokemonHp     $hp     Hit points value object.
 * @param CaptureStatus $status Capture status value object.
 *
 * Methods (behavioural overview):
 * @method static self create(PokemonId $id, PokemonName $name, PokemonType $type, PokemonHp $hp, CaptureStatus $status = CaptureStatus::WILD)
 *         Create a new Pokémon instance; status defaults to WILD.
 * @method void capture()
 *         Attempt to capture the Pokémon; if not already captured, update status and record a PokemonCaptured event.
 * @method PokemonId id() Returns the Pokémon id.
 * @method PokemonName name() Returns the Pokémon name.
 * @method PokemonType type() Returns the Pokémon type.
 * @method PokemonHp hp() Returns the Pokémon HP.
 * @method CaptureStatus status() Returns the current capture status.
 * @method array pullDomainEvents() Return recorded domain events and clear the internal events list.
 * @method bool equals(Pokemon $other) Return true if this Pokémon and $other share the same id.
 *
 * Domain events:
 * - The entity records domain events (e.g. PokemonCaptured) using a private recorder and exposes them via pullDomainEvents()
 *   so application services or a domain event dispatcher can handle them.
 */
class Pokemon
{
    private array $domainEvents = [];

    public function __construct(
        private PokemonId $id,
        private PokemonName $name,
        private PokemonType $type,
        private PokemonHp $hp,
        private CaptureStatus $status
    ) {
    }

    public static function create(
        PokemonId $id,
        PokemonName $name,
        PokemonType $type,
        PokemonHp $hp,
        CaptureStatus $status = CaptureStatus::WILD
    ): self {
        return new self($id, $name, $type, $hp, $status);
    }

    public function capture(): void
    {
        if ($this->status->isCaptured()) {
            return;
        }

        $this->status = $this->status->capture();
        $this->recordEvent(new PokemonCaptured($this->id, $this->name));
    }

    public function id(): PokemonId
    {
        return $this->id;
    }

    public function name(): PokemonName
    {
        return $this->name;
    }

    public function type(): PokemonType
    {
        return $this->type;
    }

    public function hp(): PokemonHp
    {
        return $this->hp;
    }

    public function status(): CaptureStatus
    {
        return $this->status;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function equals(Pokemon $other): bool
    {
        return $this->id->equals($other->id);
    }
}
