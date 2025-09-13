<?php

namespace App\Pokemon\Application\Command\Create;

use App\Pokemon\Application\View\PokemonView;
use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\EventBus\EventBus;
use App\Pokemon\Domain\Repository\PokemonRepository;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonName;

final readonly class CreatePokemonHandler
{
    public function __construct(
        private PokemonRepository $repository,
        private EventBus $eventBus
    ) {
    }

    /**
     * Handle creation of a new Pokemon aggregate from a CreatePokemonRequest.
     *
     * This method:
     *  - obtains a new aggregate identifier from the repository,
     *  - builds domain value objects for name, type, hp and status from the request,
     *  - constructs the Pokemon aggregate as CaptureStatus::WILD by default,
     *  - if the requested status is CaptureStatus::CAPTURED, invokes the aggregate's capture() method
     *    so the corresponding domain event is produced,
     *  - persists the aggregate using the repository,
     *  - pulls any domain events from the aggregate and dispatches them via the event bus,
     *  - creates a PokemonView from the persisted aggregate and returns a CreatePokemonResponse for that view.
     *
     * @param CreatePokemonRequest $request Request DTO containing the attributes required to create a Pokemon:
     *                                      - name: string
     *                                      - type: scalar accepted by PokemonType::from
     *                                      - hp: int
     *                                      - status: scalar accepted by CaptureStatus::from
     *
     * @return CreatePokemonResponse Response DTO constructed from the persisted Pokemon view.
     *
     * @throws \InvalidArgumentException If any value object factories (e.g. PokemonName::fromString,
     *                                   PokemonType::from, PokemonHp::fromInt, CaptureStatus::from) reject input.
     * @throws \RuntimeException If repository or event bus operations fail (e.g. nextIdentity, save, dispatchMultiple).
     *
     * Side effects:
     *  - Persists a new Pokemon aggregate.
     *  - Dispatches domain events produced by the aggregate after persistence.
     *
     * Notes:
     *  - The aggregate is created as WILD by default and only transitioned to CAPTURED by calling capture(),
     *    ensuring domain rules and events are applied consistently.
     *  - Domain events are dispatched after the aggregate is saved to ensure handlers observe the persisted state.
     */
    public function handle(CreatePokemonRequest $request): CreatePokemonResponse
    {
        $id = $this->repository->nextIdentity();
        $name = PokemonName::fromString($request->name);
        $type = PokemonType::from($request->type);
        $hp = PokemonHp::fromInt($request->hp);
        $status = CaptureStatus::from($request->status);

        // Creamos el pokemon como salvaje por defecto, "Si no recuerdo mal, es como funcionaba la pokedex"
        $pokemon = Pokemon::create($id, $name, $type, $hp, CaptureStatus::WILD);

        // Si el estado es capturado, llamamos al método capture para que se dispare el "fake evento"
        if ($status === CaptureStatus::CAPTURED) {
            $pokemon->capture();
        }

        $savedPokemon = $this->repository->save($pokemon);

        $events = $pokemon->pullDomainEvents();
        $this->eventBus->dispatchMultiple($events);

        $view = PokemonView::fromDomain($savedPokemon);

        return CreatePokemonResponse::fromView($view);
    }
}
