<?php

namespace Tests\Unit\Pokemon;

use App\Pokemon\Application\Command\Create\CreatePokemonHandler;
use App\Pokemon\Application\Command\Create\CreatePokemonRequest;
use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\EventBus\EventBus;
use App\Pokemon\Domain\Repository\PokemonRepository;
use App\Pokemon\Domain\ValueObject\PokemonId;
use Illuminate\Support\Str;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Pokemon creation handler
 * Verifies creation and persistence of Pokemon
 */
class CreatePokemonHandlerTest extends TestCase
{
    private MockObject&PokemonRepository $repositoryMock;
    private MockObject&EventBus $eventBusMock;
    private CreatePokemonHandler $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(PokemonRepository::class);
        $this->eventBusMock = $this->createMock(EventBus::class);

        $this->handler = new CreatePokemonHandler(
            $this->repositoryMock,
            $this->eventBusMock
        );
    }

    /** Verifies that a Pokemon can be created correctly */
    public function test_can_create_pokemon(): void
    {
        // Arrange
        $request = new CreatePokemonRequest(
            name: 'Pikachu',
            type: 'Electric',
            hp: 35,
            status: 'wild'
        );

    $expectedId = PokemonId::generate(); // null ID for new pokemon

        $this->repositoryMock
            ->expects($this->once())
            ->method('nextIdentity')
            ->willReturn($expectedId);

        $this->repositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Pokemon $pokemon) {
                return $pokemon->id()->value() === null &&
                       $pokemon->name()->value() === 'Pikachu' &&
                       $pokemon->type()->value === 'Electric' &&
                       $pokemon->hp()->value() === 35;
            }))
            ->willReturnCallback(function (Pokemon $pokemon) {
                // Simulate repository returning the pokemon with assigned ID
                return Pokemon::create(
                    PokemonId::fromInt(1),
                    $pokemon->name(),
                    $pokemon->type(),
                    $pokemon->hp(),
                    $pokemon->status()
                );
            });

        $this->eventBusMock
            ->expects($this->once())
            ->method('dispatchMultiple')
            ->with([]);

        // Act
        $response = $this->handler->handle($request);

        // Assert
        $this->assertEquals(1, $response->pokemon->id); 
        $this->assertEquals('Pikachu', $response->pokemon->name);
        $this->assertEquals('Electric', $response->pokemon->type);
        $this->assertEquals(35, $response->pokemon->hp);
        $this->assertEquals('wild', $response->pokemon->status);
    }

    /** Verifies that events are dispatched when a Pokemon is captured */
    public function test_dispatches_events_when_pokemon_is_captured(): void
    {
        // Arrange
        $request = new CreatePokemonRequest(
            name: 'Pikachu',
            type: 'Electric',
            hp: 35,
            status: 'captured'
        );

        $expectedId = PokemonId::generate(); 

        $this->repositoryMock
            ->method('nextIdentity')
            ->willReturn($expectedId);

        $this->repositoryMock
            ->method('save')
            ->willReturnCallback(function (Pokemon $pokemon) {
                // Simulate the repository returning the pokemon with assigned ID
                return Pokemon::create(
                    PokemonId::fromInt(1), 
                    $pokemon->name(),
                    $pokemon->type(),
                    $pokemon->hp(),
                    $pokemon->status()
                );
            });

        $this->eventBusMock
            ->expects($this->once())
            ->method('dispatchMultiple')
            ->with($this->callback(function (array $events) {
                return count($events) === 1;
            }));

        // Act
        $this->handler->handle($request);
    }
}
