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
