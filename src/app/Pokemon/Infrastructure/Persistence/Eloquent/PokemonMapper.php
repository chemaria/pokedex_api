<?php

namespace App\Pokemon\Infrastructure\Persistence\Eloquent;

use App\Models\PokemonModel;
use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonId;
use App\Pokemon\Domain\ValueObject\PokemonName;


/**
 * Maps between the Eloquent persistence representation (PokemonModel)
 * and the domain entity (Pokemon).
 *
 * Responsibilities:
 * - toDomain: create a domain Pokemon from an Eloquent model by converting
 *   primitive model values into domain value objects/enums.
 * - toEloquent: produce an associative array of scalar values suitable for
 *   Eloquent mass-assignment / persistence; omits the 'id' key when the domain
 *   entity's id is null to allow creation of new records.
 *
 * Notes:
 * - This is a stateless utility mapper implemented with static methods.
 * - It assumes the model exposes id, name, type, hp and status properties with
 *   values compatible with the corresponding domain value objects/enums.
 * - Domain factories/constructors (e.g., Pokemon::create, PokemonId::fromInt)
 *   are responsible for validating values and may throw exceptions for invalid input.
 */
 
/**
 * Convert an Eloquent PokemonModel into a domain Pokemon instance.
 *
 * @param PokemonModel $model Eloquent model containing persistence data.
 * @return Pokemon Domain entity constructed from the model values.
 *
 * @throws \TypeError If a provided value has an unexpected type.
 * @throws \InvalidArgumentException If a model value is invalid for the domain value objects/enums.
 */
 
/**
 * Convert a domain Pokemon into an associative array compatible with Eloquent.
 *
 * The returned array contains the keys:
 * - 'name'  => string
 * - 'type'  => scalar/enum backed value
 * - 'hp'    => int
 * - 'status'=> scalar/enum backed value
 *
 * The 'id' key is included only when the domain Pokemon has a non-null id value.
 *
 * @param Pokemon $pokemon Domain entity to convert.
 * @return array<string,mixed> Associative array of attribute => scalar suitable for persistence.
 */
final class PokemonMapper
{
    public static function toDomain(PokemonModel $model): Pokemon
    {
        return Pokemon::create(
            id: PokemonId::fromInt($model->id),
            name: PokemonName::fromString($model->name),
            type: PokemonType::from($model->type),
            hp: PokemonHp::fromInt($model->hp),
            status: CaptureStatus::from($model->status)
        );
    }

    public static function toEloquent(Pokemon $pokemon): array
    {
        $data = [
            'name' => $pokemon->name()->value(),
            'type' => $pokemon->type()->value,
            'hp' => $pokemon->hp()->value(),
            'status' => $pokemon->status()->value
        ];

        // Solo incluir el ID si no es null (para crear nuevos pokémon)
        if ($pokemon->id()->value() !== null) {
            $data['id'] = $pokemon->id()->value();
        }

        return $data;
    }
}
