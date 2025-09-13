<?php

namespace App\Pokemon\Domain\Repository;

use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\ValueObject\PokemonId;

interface PokemonRepository
{
    /**
     * Saves a Pokemon entity.
     *
     * @param Pokemon $pokemon The Pokemon entity to save.
     * @return Pokemon The saved Pokemon entity.
     */
    public function save(Pokemon $pokemon): Pokemon;

    /**
     * Finds a Pokemon by its ID.
     *
     * @param PokemonId $id The ID of the Pokemon to find.
     * @return Pokemon|null The found Pokemon entity or null.
     */
    public function findById(PokemonId $id): ?Pokemon;

    /**
     * Finds all Pokemon entities.
     *
     * @return Pokemon[] An array of all Pokemon entities.
    */
    public function findAll(): array;

    /**
     * Checks if a Pokemon exists by its ID.
     *
     * @param PokemonId $id The ID of the Pokemon to check.
     * @return bool True if the Pokemon exists, false otherwise.
     */
    public function exists(PokemonId $id): bool;

    /**
     * Deletes a Pokemon by its ID.
     *
     * @param PokemonId $id The ID of the Pokemon to delete.
     * @return void
     */
    public function delete(PokemonId $id): void;

    /**
     * Gets the next identity for a Pokemon.
     *
     * @return PokemonId The next Pokemon ID.
     */
    public function nextIdentity(): PokemonId;
}