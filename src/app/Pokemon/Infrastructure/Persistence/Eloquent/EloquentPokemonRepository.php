<?php

namespace App\Pokemon\Infrastructure\Persistence\Eloquent;

use App\Models\PokemonModel;
use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Repository\PokemonRepository;
use App\Pokemon\Domain\ValueObject\PokemonId;

final readonly class EloquentPokemonRepository implements PokemonRepository
{
    /**
     * Saves a Pokemon entity.
     */
    public function save(Pokemon $pokemon): Pokemon
    {
        $data = PokemonMapper::toEloquent($pokemon);
        $model = PokemonModel::create($data);
        
        // Retornar el pokemon con el ID generado
        return PokemonMapper::toDomain($model);
    }
    /**
     * Finds a Pokemon by its ID.
     */
    public function findById(PokemonId $id): ?Pokemon
    {
        if ($id->value() === null) {
            return null;
        }

        $model = PokemonModel::find($id->value());

        if ($model === null) {
            return null;
        }

        return PokemonMapper::toDomain($model);
    }
    /**
     * Finds all Pokemon entities.
     */
    public function findAll(): array
    {
        $models = PokemonModel::all();

        return $models->map(fn(PokemonModel $model) => PokemonMapper::toDomain($model))->toArray();
    }
    /**
     * Checks if a Pokemon exists by its ID.
     */
    public function exists(PokemonId $id): bool
    {
        if ($id->value() === null) {
            return false;
        }
        
        return PokemonModel::where('id', $id->value())->exists();
    }
    /**
     * Deletes a Pokemon by its ID.
     */
    public function delete(PokemonId $id): void
    {
        if ($id->value() !== null) {
            PokemonModel::where('id', $id->value())->delete();
        }
    }
    /**
     * Gets the next identity for a Pokemon.
     */
    public function nextIdentity(): PokemonId
    {
        return PokemonId::generate();
    }
}
