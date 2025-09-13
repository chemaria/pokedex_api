
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
