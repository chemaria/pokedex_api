<?php

namespace App\Pokemon\Infrastructure\Providers;

use App\Pokemon\Application\Command\Create\CreatePokemonHandler;
use App\Pokemon\Application\Query\GetById\GetPokemonByIdHandler;
use App\Pokemon\Application\Query\List\ListPokemonHandler;
use App\Pokemon\Domain\EventBus\EventBus;
use App\Pokemon\Domain\Repository\PokemonRepository;
use App\Pokemon\Infrastructure\Bus\SyncEventBus;
use App\Pokemon\Infrastructure\Persistence\Eloquent\EloquentPokemonRepository;
use Illuminate\Support\ServiceProvider;

class PokemonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindeo interface de repositorio a implementación concreta
        $this->app->bind(PokemonRepository::class, EloquentPokemonRepository::class);

        // Bindeo interface de bus de eventos a implementación concreta
        $this->app->bind(EventBus::class, SyncEventBus::class);

        // Registro de handlers
        $this->app->singleton(CreatePokemonHandler::class, function ($app) {
            return new CreatePokemonHandler(
                $app->make(PokemonRepository::class),
                $app->make(EventBus::class)
            );
        });

        $this->app->singleton(ListPokemonHandler::class, function ($app) {
            return new ListPokemonHandler(
                $app->make(PokemonRepository::class)
            );
        });

        $this->app->singleton(GetPokemonByIdHandler::class, function ($app) {
            return new GetPokemonByIdHandler(
                $app->make(PokemonRepository::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}