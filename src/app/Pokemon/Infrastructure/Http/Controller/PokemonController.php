<?php

namespace App\Pokemon\Infrastructure\Http\Controller;

use App\Http\Controllers\Controller;
use App\Pokemon\Application\Command\Create\CreatePokemonHandler;
use App\Pokemon\Application\Command\Create\CreatePokemonRequest;
use App\Pokemon\Application\Query\GetById\GetPokemonByIdHandler;
use App\Pokemon\Application\Query\GetById\GetPokemonByIdQuery;
use App\Pokemon\Application\Query\List\ListPokemonHandler;
use App\Pokemon\Application\Query\List\ListPokemonQuery;
use App\Pokemon\Domain\Exception\InvalidPokemonData;
use App\Pokemon\Domain\Exception\PokemonNotFound;
use App\Pokemon\Infrastructure\Http\Request\CreatePokemonHttpRequest;
use App\Pokemon\Infrastructure\Http\Resource\PokemonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Mini Pokédex API",
    description: "API REST para gestionar una mini Pokédex",
)]
#[OA\Server(
    url: "http://localhost:8080",
    description: "Servidor de desarrollo"
)]
class PokemonController extends Controller
{
    public function __construct(
        private readonly CreatePokemonHandler $createHandler,
        private readonly ListPokemonHandler $listHandler,
        private readonly GetPokemonByIdHandler $getByIdHandler
    ) {
    }

    #[OA\Get(
        path: "/api/pokemon",
        summary: "Listar todos los Pokémon",
        description: "Obtiene una lista de todos los Pokémon registrados",
        tags: ["Pokemon"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de Pokémon obtenida exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Pokemon")),
                        new OA\Property(property: "total", type: "integer", example: 10)
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function index(): JsonResponse
    {
        try {
            $query = new ListPokemonQuery();
            $result = $this->listHandler->handle($query);

            return response()->json([
                'data' => PokemonResource::collection($result->pokemon),
                'total' => $result->total
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve Pokemon list',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: "/api/pokemon/{id}",
        summary: "Obtener Pokémon por su id",
        description: "Obtiene la información de un Pokémon específico por su id",
        tags: ["Pokemon"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del Pokémon",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Pokémon encontrado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Pokemon")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pokémon no encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Pokemon not found"),
                        new OA\Property(property: "message", type: "string", example: "Pokemon with ID 999 not found")
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function show(string $id): JsonResponse
    {
        try {
            $query = new GetPokemonByIdQuery($id);
            $result = $this->getByIdHandler->handle($query);

            return response()->json([
                'data' => new PokemonResource($result)
            ]);
        } catch (PokemonNotFound $e) {
            return response()->json([
                'error' => 'Pokemon not found',
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve Pokemon',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Post(
        path: "/api/pokemon",
        summary: "Crear nuevo Pokémon",
        description: "Crea un nuevo Pokémon con la información proporcionada",
        tags: ["Pokemon"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreatePokemonRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Pokémon creado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Pokemon")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Datos inválidos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Invalid Pokemon data"),
                        new OA\Property(property: "message", type: "string", example: "Pokemon HP must be between 1 and 100")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function store(CreatePokemonHttpRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $command = new CreatePokemonRequest(
                name: $validated['name'],
                type: $validated['type'],
                hp: $validated['hp'],
                status: $validated['status']
            );

            $result = $this->createHandler->handle($command);

            return response()->json([
                'data' => new PokemonResource($result->pokemon)
            ], Response::HTTP_CREATED);
        } catch (InvalidPokemonData $e) {
            return response()->json([
                'error' => 'Invalid Pokemon data',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create Pokemon',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

#[OA\Schema(
    schema: "Pokemon",
    title: "Pokemon",
    description: "Modelo de un Pokémon",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Pikachu"),
        new OA\Property(property: "type", type: "string", enum: ["Electric", "Fire", "Water", "Grass", "Rock", "Flying", "Bug", "Normal", "Fighting", "Poison", "Ground", "Psychic", "Ice", "Dragon", "Dark", "Steel", "Fairy"], example: "Electric"),
        new OA\Property(property: "hp", type: "integer", minimum: 1, maximum: 100, example: 35),
        new OA\Property(property: "status", type: "string", enum: ["wild", "captured"], example: "captured")
    ]
)]
class PokemonSchema {}

#[OA\Schema(
    schema: "CreatePokemonRequest",
    title: "Crear Pokémon Request",
    description: "Datos requeridos para crear un nuevo Pokémon",
    required: ["name", "type", "hp"],
    properties: [
        new OA\Property(property: "name", type: "string", maxLength: 50, example: "Charizard"),
        new OA\Property(property: "type", type: "string", enum: ["Electric", "Fire", "Water", "Grass", "Rock", "Flying", "Bug", "Normal", "Fighting", "Poison", "Ground", "Psychic", "Ice", "Dragon", "Dark", "Steel", "Fairy"], example: "Fire"),
        new OA\Property(property: "hp", type: "integer", minimum: 1, maximum: 100, example: 78),
        new OA\Property(property: "status", type: "string", enum: ["wild", "captured"], example: "wild", description: "Estado de captura (por defecto: wild)")
    ]
)]
class CreatePokemonRequestSchema {}
