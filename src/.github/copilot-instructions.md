<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.3
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit <name>` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== .ai/hex-ddd-cqs rules ===

# 🧱 Arquitectura del Proyecto — Hexagonal + DDD + CQS (API Laravel)

> **Scope**: API REST **sin frontend**, solo backend Laravel.  
> **Objetivo**: Código limpio, capas bien separadas, testing y SOLID.  
> **Dominio**: Mini Pokédex (Pokemon CRUD mínimo: listar, obtener, crear).

---

## 📦 Estructura por Feature (Feature-Based)

Cada **feature** encapsula Domain / Application / Infrastructure, con dependencias apuntando hacia el Dominio.

```
app/
  Pokemon/
    Domain/
      Entity/
        Pokemon.php
      ValueObject/
        PokemonName.php
        PokemonHp.php
      Enum/
        PokemonType.php
        CaptureStatus.php
      Event/
        PokemonCaptured.php
      Repository/
        PokemonRepository.php        // Puerto (interfaz)
      Service/
        // (Opcional si hay reglas multi-agregado)
      Exception/
        PokemonNotFound.php
        DomainException.php
      EventBus/                       // Puerto de publicación de eventos
        EventBus.php
    Application/
      Command/
        Create/
          CreatePokemonRequest.php    // DTO entrada (datos puros)
          CreatePokemonHandler.php    // Orquestación/uso de dominio
          CreatePokemonResponse.php   // DTO salida
      Query/
        GetById/
          GetPokemonByIdQuery.php
          GetPokemonByIdHandler.php
          PokemonView.php             // DTO lectura
        List/
          ListPokemonQuery.php
          ListPokemonHandler.php
          PokemonListView.php         // DTO lectura (colección)
      Middleware/
        TransactionalCommandMiddleware.php // Decorador transaccional SOLO para Commands
    Infrastructure/
      Persistence/
        Eloquent/
          Model/
            PokemonModel.php
          Repository/
            EloquentPokemonRepository.php  // Adaptador al puerto
          Mapper/
            PokemonMapper.php              // Entidad <-> Model
      Http/
        Controller/
          PokemonController.php            // Controlador fino (validación + delegar)
        Request/
          CreatePokemonHttpRequest.php     // Validación HTTP
        Resource/
          PokemonResource.php              // Salida API
      Bus/
        SyncEventBus.php                   // Adaptador EventBus (sync)
      Providers/
        PokemonServiceProvider.php         // Bindings de puertos -> adaptadores
routes/
  api.php                                  // Rutas REST
database/
  migrations/xxxx_create_pokemon_table.php
  migrations/xxxx_create_outbox_table.php  // (opcional)
config/
  pokemon.php                              // (opcional) parámetros de feature
```

**Reglas clave**  
- Dominio **no** conoce Eloquent/Laravel. Solo interfaces y tipos puros.  
- Application **orquesta** casos de uso: **Command** (escritura) envuelto en transacción; **Query** (lectura) fuera de transacción.  
- Infrastructure contiene controladores, validación HTTP, Eloquent y mapeadores.

---

## 🧠 Dominio (Reglas de negocio puras)

### Entidad Agregado `Pokemon`
Campos: `id (Identity)`, `name (VO)`, `type (Enum)`, `hp (VO)`, `status (Enum)`.

```php
<?php
// app/Pokemon/Domain/Entity/Pokemon.php
declare(strict_types=1);

namespace App\Pokemon\Domain\Entity;

use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonName;
use App\Pokemon\Domain\Event\PokemonCaptured;

final class Pokemon
{
    public function __construct(
        private ?int $id,
        private PokemonName $name,
        private PokemonType $type,
        private PokemonHp $hp,
        private CaptureStatus $status
    ) {}

    public static function wild(PokemonName $name, PokemonType $type, PokemonHp $hp): self
    {
        return new self(null, $name, $type, $hp, CaptureStatus::Wild);
    }

    public function capture(): ?PokemonCaptured
    {
        if ($this->status === CaptureStatus::Captured) {
            return null;
        }
        $this->status = CaptureStatus::Captured;
        return new PokemonCaptured($this->id, (string)$this->name);
    }

    // Getters inmutables
    public function id(): ?int { return $this->id; }
    public function name(): PokemonName { return $this->name; }
    public function type(): PokemonType { return $this->type; }
    public function hp(): PokemonHp { return $this->hp; }
    public function status(): CaptureStatus { return $this->status; }

    // Solo infraestructura debería establecer ID tras persistir
    public function withId(int $id): self { $clone = clone $this; $clone->id = $id; return $clone; }
}
```

### Value Objects y Enums

```php
// app/Pokemon/Domain/ValueObject/PokemonName.php
final class PokemonName {
    public function __construct(private string $value) {
        $v = trim($value);
        if ($v === '' || mb_strlen($v) > 60) {
            throw new \InvalidArgumentException('Invalid Pokemon name');
        }
        $this->value = $v;
    }
    public function __toString(): string { return $this->value; }
}

// app/Pokemon/Domain/ValueObject/PokemonHp.php
final class PokemonHp {
    public function __construct(private int $value) {
        if ($value < 1 || $value > 100) throw new \InvalidArgumentException('HP must be 1..100');
    }
    public function toInt(): int { return $this->value; }
}

// app/Pokemon/Domain/Enum/PokemonType.php
enum PokemonType: string { case Electric='Electric'; case Fire='Fire'; case Water='Water'; /* extiende según necesidad */ }

// app/Pokemon/Domain/Enum/CaptureStatus.php
enum CaptureStatus: string { case Wild='wild'; case Captured='captured'; }
```

### Puertos del Dominio

```php
// app/Pokemon/Domain/Repository/PokemonRepository.php
interface PokemonRepository {
    public function save(\App\Pokemon\Domain\Entity\Pokemon $pokemon): \App\Pokemon\Domain\Entity\Pokemon; // devuelve con ID
    public function findById(int $id): \App\Pokemon\Domain\Entity\Pokemon; // lanzar PokemonNotFound
    /** @return \App\Pokemon\Domain\Entity\Pokemon[] */
    public function all(): array;
}

// app/Pokemon/Domain/EventBus/EventBus.php
interface EventBus { public function publish(object $domainEvent): void; }
```

---

## 🧭 Capa de Aplicación (Use Cases)

### Command: `CreatePokemon`

```php
// Application/Command/Create/CreatePokemonRequest.php
final class CreatePokemonRequest {
    public function __construct(public string $name, public string $type, public int $hp, public string $status) {}
}

// Application/Command/Create/CreatePokemonHandler.php
final class CreatePokemonHandler
{
    public function __construct(
        private \App\Pokemon\Domain\Repository\PokemonRepository $repo,
        private \App\Pokemon\Domain\EventBus\EventBus $bus
    ) {}

    public function __invoke(CreatePokemonRequest $req): CreatePokemonResponse
    {
        $pokemon = new \App\Pokemon\Domain\Entity\Pokemon(
            null,
            new \App\Pokemon\Domain\ValueObject\PokemonName($req->name),
            \App\Pokemon\Domain\Enum\PokemonType::from($req->type),
            new \App\Pokemon\Domain\ValueObject\PokemonHp($req->hp),
            \App\Pokemon\Domain\Enum\CaptureStatus::from($req->status)
        );

        // Regla de negocio opcional
        $event = $pokemon->status() === \App\Pokemon\Domain\Enum\CaptureStatus::Captured
            ? $pokemon->capture()
            : null;

        $saved = $this->repo->save($pokemon);

        if ($event) { $this->bus->publish($event); }

        return new CreatePokemonResponse($saved->id(), (string)$saved->name(), $saved->type()->value, $saved->hp()->toInt(), $saved->status()->value);
    }
}

// Application/Command/Create/CreatePokemonResponse.php
final class CreatePokemonResponse {
    public function __construct(
        public int $id, public string $name, public string $type, public int $hp, public string $status
    ) {}
}
```

### Queries: `GetPokemonById`, `ListPokemon`

```php
// Application/Query/GetById/GetPokemonByIdQuery.php
final class GetPokemonByIdQuery { public function __construct(public int $id) {} }

// Application/Query/GetById/GetPokemonByIdHandler.php
final class GetPokemonByIdHandler
{
    public function __construct(private \App\Pokemon\Domain\Repository\PokemonRepository $repo) {}
    public function __invoke(GetPokemonByIdQuery $q): PokemonView {
        $p = $this->repo->findById($q->id);
        return new PokemonView($p->id(), (string)$p->name(), $p->type()->value, $p->hp()->toInt(), $p->status()->value);
    }
}

// Application/Query/List/ListPokemonQuery.php
final class ListPokemonQuery {}

// Application/Query/List/ListPokemonHandler.php
final class ListPokemonHandler
{
    public function __construct(private \App\Pokemon\Domain\Repository\PokemonRepository $repo) {}
    public function __invoke(ListPokemonQuery $q): PokemonListView {
        $all = array_map(fn($p) => new PokemonView($p->id(), (string)$p->name(), $p->type()->value, $p->hp()->toInt(), $p->status()->value), $this->repo->all());
        return new PokemonListView($all);
    }
}

// Application/Query/View DTOs
final class PokemonView {
    public function __construct(public int $id, public string $name, public string $type, public int $hp, public string $status) {}
}
final class PokemonListView {
    /** @param PokemonView[] $items */
    public function __construct(public array $items) {}
}
```

### Decorador Transaccional (solo Commands)

```php
// Application/Middleware/TransactionalCommandMiddleware.php
namespace App\Pokemon\Application\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Envuélvelo alrededor de handlers de COMMAND (escritura).
 * Uso típico desde un CommandBus simple o manualmente en el controlador.
 */
final class TransactionalCommandMiddleware
{
    public function handle(object $command, Closure $next)
    {
        return DB::transaction(fn() => $next($command));
    }
}
```

---

## 🛠️ Infraestructura (Adaptadores)

### Eloquent Model + Mapper + Repo

```php
// Infrastructure/Persistence/Eloquent/Model/PokemonModel.php
namespace App\Pokemon\Infrastructure\Persistence\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

class PokemonModel extends Model
{
    protected $table = 'pokemon';
    protected $fillable = ['name','type','hp','status'];
}

// Infrastructure/Persistence/Eloquent/Mapper/PokemonMapper.php
namespace App\Pokemon\Infrastructure\Persistence\Eloquent\Mapper;

use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Domain\ValueObject\PokemonHp;
use App\Pokemon\Domain\ValueObject\PokemonName;
use App\Pokemon\Infrastructure\Persistence\Eloquent\Model\PokemonModel;

final class PokemonMapper {
    public static function toModel(Pokemon $e): PokemonModel {
        $m = new PokemonModel([
            'name' => (string)$e->name(),
            'type' => $e->type()->value,
            'hp' => $e->hp()->toInt(),
            'status' => $e->status()->value,
        ]);
        if ($e->id()) { $m->id = $e->id(); }
        return $m;
    }
    public static function toEntity(PokemonModel $m): Pokemon {
        return (new Pokemon(
            $m->id,
            new PokemonName($m->name),
            PokemonType::from($m->type),
            new PokemonHp((int)$m->hp),
            CaptureStatus::from($m->status)
        ));
    }
}

// Infrastructure/Persistence/Eloquent/Repository/EloquentPokemonRepository.php
namespace App\Pokemon\Infrastructure\Persistence\Eloquent\Repository;

use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\Exception\PokemonNotFound;
use App\Pokemon\Domain\Repository\PokemonRepository;
use App\Pokemon\Infrastructure\Persistence\Eloquent\Mapper\PokemonMapper;
use App\Pokemon\Infrastructure\Persistence\Eloquent\Model\PokemonModel;

final class EloquentPokemonRepository implements PokemonRepository
{
    public function save(Pokemon $pokemon): Pokemon
    {
        $model = PokemonMapper::toModel($pokemon);
        $model->save();
        return PokemonMapper::toEntity($model);
    }
    public function findById(int $id): Pokemon
    {
        $m = PokemonModel::find($id);
        if (!$m) { throw new PokemonNotFound($id); }
        return PokemonMapper::toEntity($m);
    }
    public function all(): array
    {
        return array_map(fn($m) => PokemonMapper::toEntity($m), PokemonModel::query()->orderBy('id')->get()->all());
    }
}
```

### EventBus (sincrónico mínimo)

```php
// Infrastructure/Bus/SyncEventBus.php
namespace App\Pokemon\Infrastructure\Bus;

use App\Pokemon\Domain\EventBus\EventBus;

final class SyncEventBus implements EventBus {
    public function publish(object $domainEvent): void {
        logger()->info('DomainEvent', ['event' => $domainEvent::class, 'payload' => (array)$domainEvent]);
    }
}
```

### Controlador HTTP + Validación + Resource

```php
// Infrastructure/Http/Request/CreatePokemonHttpRequest.php
namespace App\Pokemon\Infrastructure\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class CreatePokemonHttpRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => ['required','string','max:60'],
            'type' => ['required','string'],
            'hp'   => ['required','integer','min:1','max:100'],
            'status'=>['required','string'],
        ];
    }
    public function authorize(): bool { return true; }
}

// Infrastructure/Http/Controller/PokemonController.php
namespace App\Pokemon\Infrastructure\Http\Controller;

use App\Http\Controllers\Controller;
use App\Pokemon\Application\Command\Create\CreatePokemonHandler;
use App\Pokemon\Application\Command\Create\CreatePokemonRequest;
use App\Pokemon\Application\Middleware\TransactionalCommandMiddleware;
use App\Pokemon\Application\Query\GetById\GetPokemonByIdHandler;
use App\Pokemon\Application\Query\GetById\GetPokemonByIdQuery;
use App\Pokemon\Application\Query\List\ListPokemonHandler;
use App\Pokemon\Application\Query\List\ListPokemonQuery;
use App\Pokemon\Domain\Enum\CaptureStatus;
use App\Pokemon\Domain\Enum\PokemonType;
use App\Pokemon\Infrastructure\Http\Request\CreatePokemonHttpRequest;
use App\Pokemon\Infrastructure\Http\Resource\PokemonResource;
use Illuminate\Http\JsonResponse;

final class PokemonController extends Controller
{
    public function __construct(
        private ListPokemonHandler $list,
        private GetPokemonByIdHandler $getById,
        private CreatePokemonHandler $create,
        private TransactionalCommandMiddleware $tx
    ) {}

    public function index(): JsonResponse
    {
        $view = ($this->list)(new ListPokemonQuery());
        return response()->json(array_map(fn($v) => (new PokemonResource($v))->toArray(request()), $view->items));
    }

    public function show(int $id): JsonResponse
    {
        $view = ($this->getById)(new GetPokemonByIdQuery($id));
        return response()->json((new PokemonResource($view))->toArray(request()));
    }

    public function store(CreatePokemonHttpRequest $req): JsonResponse
    {
        // Validaciones enum
        $type = PokemonType::from($req->string('type'));
        $status = CaptureStatus::from($req->string('status'));

        $command = new CreatePokemonRequest(
            $req->string('name'),
            $type->value,
            $req->integer('hp'),
            $status->value
        );

        $resp = $this->tx->handle($command, fn($c) => ($this->create)($c));

        return response()->json($resp, 201);
    }
}

// Infrastructure/Http/Resource/PokemonResource.php
namespace App\Pokemon\Infrastructure\Http\Resource;

use Illuminate\Http\Resources\Json\JsonResource;

class PokemonResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'=>$this->id, 'name'=>$this->name, 'type'=>$this->type, 'hp'=>$this->hp, 'status'=>$this->status
        ];
    }
}
```

### Routes + Provider

```php
// routes/api.php
use App\Pokemon\Infrastructure\Http\Controller\PokemonController;
use Illuminate\Support\Facades\Route;

Route::get('/pokemon', [PokemonController::class,'index']);
Route::get('/pokemon/{id}', [PokemonController::class,'show'])->whereNumber('id');
Route::post('/pokemon', [PokemonController::class,'store']);

// Infrastructure/Providers/PokemonServiceProvider.php
namespace App\Pokemon\Infrastructure\Providers;

use App\Pokemon\Domain\EventBus\EventBus;
use App\Pokemon\Domain\Repository\PokemonRepository;
use App\Pokemon\Infrastructure\Bus\SyncEventBus;
use App\Pokemon\Infrastructure\Persistence\Eloquent\Repository\EloquentPokemonRepository;
use Illuminate\Support\ServiceProvider;

final class PokemonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PokemonRepository::class, EloquentPokemonRepository::class);
        $this->app->bind(EventBus::class, SyncEventBus::class);
    }
    public function boot(): void {}
}
// config/app.php -> providers[] = App\Pokemon\Infrastructure\Providers\PokemonServiceProvider::class
```

### Migración

```php
// database/migrations/xxxx_create_pokemon_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pokemon', function (Blueprint $t) {
            $t->id();
            $t->string('name',60);
            $t->string('type',32);
            $t->unsignedTinyInteger('hp');
            $t->string('status',16);
            $t->timestamps();
            $t->index(['type','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('pokemon'); }
};
```
---

## 🚦 Manejo de Errores & Mapeo HTTP

- `PokemonNotFound` → HTTP **404**.  
- `InvalidArgumentException` / validaciones VO → HTTP **422**.  
- Resto → HTTP **500**.

Usa `app/Exceptions/Handler.php` para mapear excepciones de dominio a respuestas JSON coherentes.

---

## 📏 Principios SOLID & Clean Code

- **SRP**: Handler por caso de uso. Controladores “finos/cortos”.  
- **OCP**: Extiende añadiendo nuevos Commands/Queries; evita modificar existentes.  
- **LSP**: Repositorio en memoria en tests debe comportarse como el Eloquent.  
- **ISP**: Puertos pequeños y específicos (`PokemonRepository`, `EventBus`).  
- **DIP**: El Dominio depende de **interfaces**, Infraestructura provee implementaciones.

---

## 📤 Eventos de Dominio & (opcional) Outbox

MVP: `SyncEventBus` con log.  
Siguiente paso: persistir eventos (`outbox`) y procesarlos asíncronamente (queue).  
Esto garantiza **entrega al menos una vez** y reintentos sin perder eventos.

---

## 🧹 Estática & Estilo recomendados

- **Larastan** (phpstan) nivel 6+  
- **Laravel Pint** (o PHP CS Fixer)  
- **Rector** (opcional para modernizaciones)

---

## 🗂️ README y PROMPTS

- `README.md`: setup, migraciones, comandos, rutas.  
- `PROMPTS.md`: guarda **todos** los prompts de IA usados y anota las decisiones que tomaste al refinar el output (no vibe coding).


=== .ai/testing-strategy rules ===

# ✅ Estrategia de Testing — Mini Pokédex API (Laravel)

> **Objetivo**: Garantizar calidad con pruebas **unitarias**, **integración/adaptadores** y **feature/E2E HTTP**.  
> **Requisito** de la prueba técnica:  
> - ≥ 1 **unit** test mockeando dependencias  
> - ≥ 1 **functional/E2E** de endpoints

---

## 🧪 Pirámide de Tests

1. **Unit (rápidos, aislados)**
   - Lógica de **Dominio** (Entidades, VOs, reglas).
   - **Application** (Handlers) con **dobles** (mocks/fakes) de puertos.
2. **Integración / Adaptadores**
   - Repositorio Eloquent ↔ DB real (SQLite o MySQL local).
   - Mapper Entidad↔Modelo.
3. **Feature / E2E (HTTP)**
   - Rutas reales (`/api/pokemon`) con validaciones, controllers y respuestas JSON.

---

## ⚙️ Configuración Testing

- `phpunit.xml` → DB de tests; por defecto, Laravel usa SQLite en memoria si no se configura.
- Migraciones para tests: `php artisan migrate:fresh --env=testing` (se ejecuta automáticamente con `RefreshDatabase`).
- Recomendado: `tests/` estructurado por capas:

```
tests/
  Unit/
    Pokemon/
      Domain/
      Application/
  Integration/
    Pokemon/Infrastructure/
  Feature/
    Pokemon/Http/
```
---

## 🧩 Dobles de Test (Mocks/Fakes)

- **InMemoryPokemonRepository**: implementación simple que guarda en array, para tests de aplicación y dominio.
- **FakeEventBus**: acumula eventos publicados para aserciones.
- **Mothers/Builders**: `PokemonMother`, `PokemonRequestMother` para generar datos consistentes.

```php
// tests/Doubles/InMemoryPokemonRepository.php
final class InMemoryPokemonRepository implements \App\Pokemon\Domain\Repository\PokemonRepository
{
    /** @var array<int,\App\Pokemon\Domain\Entity\Pokemon> */
    private array $data = [];
    private int $autoId = 1;

    public function save($pokemon): \App\Pokemon\Domain\Entity\Pokemon {
        $id = $pokemon->id() ?? $this->autoId++;
        $saved = $pokemon->withId($id);
        $this->data[$id] = $saved;
        return $saved;
    }
    public function findById(int $id): \App\Pokemon\Domain\Entity\Pokemon {
        if (!isset($this->data[$id])) throw new \App\Pokemon\Domain\Exception\PokemonNotFound($id);
        return $this->data[$id];
    }
    public function all(): array { return array_values($this->data); }
}

// tests/Doubles/FakeEventBus.php
final class FakeEventBus implements \App\Pokemon\Domain\EventBus\EventBus {
    /** @var object[] */ public array $published = [];
    public function publish(object $e): void { $this->published[] = $e; }
}
```

---

## 🧪 Unit Test — Handler con dependencias mockeadas

```php
// tests/Unit/Pokemon/Application/CreatePokemonHandlerTest.php
use App\Pokemon\Application\Command\Create\{CreatePokemonHandler, CreatePokemonRequest};
use App\Pokemon\Domain/Event/PokemonCaptured;

it('creates a captured pokemon and publishes event', function () {
    $repo = new InMemoryPokemonRepository();
    $bus  = new FakeEventBus();

    $handler = new CreatePokemonHandler($repo, $bus);

    $resp = $handler(new CreatePokemonRequest('Pikachu', 'Electric', 35, 'captured'));

    expect($resp->id)->toBeInt()->and($resp->name)->toBe('Pikachu');
    // Event publicado
    $classes = array_map(fn($e) => $e::class, $bus->published);
    expect($classes)->toContain(PokemonCaptured::class);
});
```
*(Ejemplo con Pest; equivalente en PHPUnit usando asserts si prefieres).*

---

## 🔌 Integración — Repositorio Eloquent

```php
// tests/Integration/Pokemon/Infrastructure/EloquentPokemonRepositoryTest.php
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Pokemon\Infrastructure\Persistence\Eloquent\Repository\EloquentPokemonRepository;
use App\Pokemon\Domain\Entity\Pokemon;
use App\Pokemon\Domain\ValueObject\{PokemonName, PokemonHp};
use App\Pokemon\Domain\Enum\{PokemonType, CaptureStatus};

uses(RefreshDatabase::class);

it('persists and retrieves a pokemon via eloquent', function () {
    $repo = app(EloquentPokemonRepository::class);

    $saved = $repo->save(Pokemon::wild(new PokemonName('Charmander'), PokemonType::Fire, new PokemonHp(39)));
    $found = $repo->findById($saved->id());

    expect($found->id())->toBe($saved->id())
        ->and((string)$found->name())->toBe('Charmander')
        ->and($found->type()->value)->toBe('Fire')
        ->and($found->hp()->toInt())->toBe(39)
        ->and($found->status()->value)->toBe('wild');
});
```

---

## 🌐 Feature/E2E — HTTP endpoints

```php
// tests/Feature/Pokemon/Http/PokemonEndpointsTest.php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('POST /api/pokemon creates and returns 201', function () {
    $payload = ['name'=>'Bulbasaur','type'=>'Water','hp'=>45,'status'=>'wild'];
    $res = $this->postJson('/api/pokemon', $payload);
    $res->assertCreated()->assertJsonStructure(['id','name','type','hp','status']);
});

it('GET /api/pokemon lists', function () {
    $this->postJson('/api/pokemon', ['name'=>'Pika','type'=>'Electric','hp'=>35,'status'=>'captured']);
    $res = $this->getJson('/api/pokemon');
    $res->assertOk()->assertJsonIsArray();
});

it('GET /api/pokemon/{id} returns 404 when missing', function () {
    $this->getJson('/api/pokemon/999')->assertNotFound();
});
```

---

## 🧷 Validación y Mapeo de Errores en Tests

- Testea **422** cuando `hp` fuera de rango o `name` vacío.
- Testea **404** para inexistentes.
- Testea **201** + **body exacto** en creación.

Usa `assertJsonPath()` / `assertExactJson()`; en listados `assertJsonStructure(['*'=>['id','name','type','hp','status']])`.

---

## 📈 Cobertura & CI (GitHub Actions sugerido)

```yaml
# .github/workflows/tests.yml
name: tests
on: [push, pull_request]
jobs:
  phpunit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.3', extensions: mbstring, coverage: xdebug }
      - run: composer install --prefer-dist --no-interaction
      - run: cp .env.example .env && php artisan key:generate
      - run: php artisan migrate --force
      - run: php artisan test --coverage-text
```

---

## 🔍 Estática y calidad en CI

- **Larastan** (phpstan) nivel 6+.  
- **Laravel Pint** / **PHP CS Fixer** en pre-commit.  
- Falla el pipeline si hay errores de análisis o formateo.

---

## 🧠 IA y Testing

- Guarda **prompts** en `PROMPTS.md`.  
- Checklist en PRs: *tests añadidos/pasados*, *estática OK*, *cobertura no baja*.  
- Evita “vibe coding”: adapta y **justifica** decisiones.

---

## ▶️ Comandos útiles

- Ejecutar toda la suite: `php artisan test`  
- Solo unit: `php artisan test --testsuite=Unit`  
- Solo feature: `php artisan test --testsuite=Feature`  
- Cobertura: `XDEBUG_MODE=coverage php artisan test --coverage-html coverage`
</laravel-boost-guidelines>