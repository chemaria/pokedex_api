# 🎯 Tasks - Mini Pokédex API

## 📋 Contexto del Proyecto

API REST para gestionar información de Pokémon siguiendo principios de **Clean Code**, **Clean Architecture** (Hexagonal + DDD + CQS) y **Testing**.

---

## 🏗️ Estructura de Implementación

### ✅ 1. Setup Inicial y Base de Datos

- [x] Validaciones de base de datos (unique, not null, etc.)
- [x] Crear factory para `PokemonModel`
- [x] Crear seeder con datos de ejemplo
- [x] Ejecutar migraciones y seeders

### ✅ 2. Dominio (Core Business Logic)

- [x] **Entidades y Agregados**
  - [x] `Pokemon` entity con métodos de dominio
  - [x] `PokemonId` identity value object
- [x] **Value Objects**
  - [x] `PokemonName` (validación nombre)
  - [x] `PokemonHp` (validación rango 1-100)
- [x] **Enums**
  - [x] `PokemonType` (Electric, Fire, Water, etc.)
  - [x] `CaptureStatus` (wild, captured)
- [x] **Puertos del Dominio**
  - [x] `PokemonRepository` interface
  - [x] `EventBus` interface
- [x] **Domain Events**
  - [x] `PokemonCaptured` event
- [x] **Excepciones del Dominio**
  - [x] `PokemonNotFound`
  - [x] `InvalidPokemonData`

### ✅ 3. Capa de Aplicación (Use Cases)

- [x] **Commands (Escritura)**
  - [x] `CreatePokemonRequest/Handler/Response`
- [x] **Queries (Lectura)**
  - [x] `ListPokemonQuery/Handler`
  - [x] `GetPokemonByIdQuery/Handler`
- [x] **DTOs/Views**
  - [x] `PokemonView` (para respuestas)
  - [x] `PokemonListView` (para listados)
- [x] **Middleware Transaccional**
  - [x] `TransactionalCommandMiddleware` (OJO!! SOLO para Commands)

### ✅ 4. Infraestructura (Adaptadores)

- [x] **Persistencia**
  - [x] `PokemonModel` (Eloquent)
  - [x] `PokemonMapper` (Domain ↔ Eloquent)
  - [x] `EloquentPokemonRepository` (implementa interface)
- [x] **HTTP Layer**
  - [x] `PokemonController` con métodos REST
  - [x] `CreatePokemonHttpRequest` (validación HTTP)
  - [x] `PokemonResource` (transformación JSON)
- [x] **EventBus**
  - [x] `SyncEventBus` (implementación simple)
- [x] **Service Provider**
  - [x] `PokemonServiceProvider` (binding interfaces)
- [x] **Rutas API**
  - [x] Definir rutas en `routes/api.php`

### ✅ 5. Testing

- [x] **Tests Unitarios**
  - [x] Test de Value Objects (`PokemonName`, `PokemonHp`)
  - [x] Test de Entidad `Pokemon`
  - [x] Test de Handlers con mocks
  - [x] Test de `PokemonMapper`
- [x] **Tests de Integración**
  - [x] Test de `EloquentPokemonRepository`
  - [x] Test de migraciones y seeders
- [x] **Tests Funcionales/E2E**
  - [x] `GET /api/pokemon` - Listar todos
  - [x] `GET /api/pokemon/{id}` - Obtener por ID
  - [x] `POST /api/pokemon` - Crear nuevo
  - [x] Casos de error (404, validación, etc.)

### ✅ 6. Documentación

- [x] **README.md**
  - [x] Instrucciones de instalación
  - [x] Comandos para ejecutar proyecto (Proyecto dockerizado, Makefile ../Makefile)
  - [x] Descripción de arquitectura
  - [x] Cómo ejecutar tests
- [x] **PROMPTS.md**
  - [x] Documentar todos los prompts de IA utilizados
  - [x] Explicar decisiones tomadas con IA
- [x] **API Documentation**
  - [x] Documentar endpoints con ejemplos
  - [x] Swagger

---

## 🚀 Endpoints a Implementar

### 1. `GET /api/pokemon`

**Funcionalidad**: Listar todos los Pokémon

- **Query Handler**: `ListPokemonHandler`
- **Response**: Array de `PokemonView`
- **Test**: Verificar listado completo

### 2. `GET /api/pokemon/{id}`

**Funcionalidad**: Obtener Pokémon por ID

- **Query Handler**: `GetPokemonByIdHandler`
- **Response**: `PokemonView` o 404
- **Test**: Pokémon existente y no existente

### 3. `POST /api/pokemon`

**Funcionalidad**: Crear nuevo Pokémon

- **Command Handler**: `CreatePokemonHandler`
- **Request**: `CreatePokemonHttpRequest`
- **Response**: `PokemonView` creado (201)
- **Test**: Creación exitosa y validaciones

---

## 📝 Modelo de Datos

```json
{
  "id": 1,
  "name": "Pikachu",
  "type": "Electric",
  "hp": 35,
  "status": "captured"
}
```

**Validaciones**:

- `name`: string, required, max:50, unique
- `type`: enum (Electric, Fire, Water, Grass, Rock, etc.)
- `hp`: integer, min:1, max:100
- `status`: enum (wild, captured)

---

## 🏃‍♂️ Orden de Implementación Sugerido

1. **Dominio primero** (Value Objects, Entidades, Interfaces)
2. **Infraestructura de persistencia** (Model, Mapper, Repository)
3. **Casos de uso** (Handlers de Commands y Queries)
4. **Layer HTTP** (Controller, Requests, Resources)
5. **Binding y rutas** (Service Provider, routes/api.php)
6. **Testing** (Unit → Integration → E2E)
7. **Documentación final**

---

## 🔧 Comandos Útiles

```bash
# Crear migración
php artisan make:migration create_pokemon_table

# Crear factory
php artisan make:factory PokemonModelFactory

# Crear seeder
php artisan make:seeder PokemonSeeder

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed --class=PokemonSeeder

# Ejecutar tests
php artisan test

# Ejecutar test específico
php artisan test --filter=PokemonTest

# Formatear código
vendor/bin/pint --dirty
```

---

## 📦 Estructura Final Esperada

```
app/Pokemon/
├── Domain/
│   ├── Entity/Pokemon.php
│   ├── ValueObject/PokemonName.php
│   ├── ValueObject/PokemonHp.php
│   ├── Enum/PokemonType.php
│   ├── Enum/CaptureStatus.php
│   ├── Repository/PokemonRepository.php
│   ├── EventBus/EventBus.php
│   └── Exception/PokemonNotFound.php
├── Application/
│   ├── Command/Create/
│   ├── Query/List/
│   ├── Query/GetById/
│   └── Middleware/TransactionalCommandMiddleware.php
└── Infrastructure/
    ├── Persistence/Eloquent/
    ├── Http/Controller/
    ├── Http/Request/
    ├── Http/Resource/
    ├── Bus/SyncEventBus.php
    └── Providers/PokemonServiceProvider.php
```

---

## ✅ Criterios de Aceptación

- [ ] ✅ **Arquitectura limpia**: Dominio independiente, capas bien separadas
- [ ] ✅ **Clean code**: Métodos pequeños, nombres descriptivos
- [ ] ✅ **Tests**: Al menos 1 unitario mockeando + 1 funcional E2E
- [ ] ✅ **Documentación IA**: Todos los prompts documentados en PROMPTS.md
- [ ] ✅ **Funcionalidad**: 3 endpoints REST funcionando correctamente
- [ ] ✅ **Validaciones**: Datos correctamente validados
- [ ] ✅ **Setup**: Instrucciones claras en README.md
