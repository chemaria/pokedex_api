# 🔴 Mini Pokédex API

Una API REST para gestionar información de Pokémon, desarrollada siguiendo principios de **Clean Code**, **Clean Architecture** (Hexagonal + DDD + CQS) y **Testing**.

## 🏗️ Arquitectura

El proyecto implementa una arquitectura hexagonal con las siguientes capas:

```
app/Pokemon/
├── Domain/                    # Lógica de negocio pura
│   ├── Entity/               # Entidades y agregados
│   ├── ValueObject/          # Value Objects con validaciones
│   ├── Enum/                 # Enumeraciones
│   ├── Repository/           # Interfaces de repositorio
│   ├── EventBus/            # Interface de event bus
│   ├── Event/               # Eventos de dominio
│   └── Exception/           # Excepciones específicas del dominio
├── Application/              # Casos de uso
│   ├── Command/             # Commands (escritura)
│   ├── Query/               # Queries (lectura)
│   ├── View/                # DTOs para respuestas
│   └── Middleware/          # Middleware transaccional
└── Infrastructure/          # Adaptadores
    ├── Persistence/         # Implementación con Eloquent
    ├── Http/                # Controllers, Requests, Resources
    ├── Bus/                 # Event Bus sincrónico
    └── Providers/           # Service Provider
```

## 📊 Modelo de Datos

Cada Pokémon tiene los siguientes campos:

```json
{
  "id": 1,
  "name": "Pikachu",
  "type": "Electric",
  "hp": 35,
  "status": "captured"
}
```

### Validaciones

- **name**: string, requerido, máximo 50 caracteres, único
- **type**: enum (Electric, Fire, Water, Grass, Rock, Flying, Bug, Normal, Fighting, Poison, Ground, Psychic, Ice, Dragon, Dark, Steel, Fairy)
- **hp**: entero, mínimo 1, máximo 100
- **status**: enum (wild, captured), por defecto 'wild'

## � Documentación de la API

### 🔗 Acceso a Swagger UI
Una vez que el proyecto esté ejecutándose, puedes acceder a la documentación interactiva de Swagger:

**URL**: `http://localhost:8080/api/documentation`

La documentación incluye:
- 📋 Lista completa de endpoints
- 🧪 Interfaz para probar los endpoints directamente
- 📝 Esquemas de datos de request/response
- ⚠️ Códigos de error y respuestas

### 🧪 Probar la API desde Swagger

1. **Accede a**: `http://localhost:8080/api/documentation`
2. **Explora los endpoints** disponibles en la sección "Pokemon"
3. **Haz clic en "Try it out"** en cualquier endpoint
4. **Modifica los parámetros** según necesites
5. **Ejecuta** y ve la respuesta en tiempo real

### 🌐 Endpoints Principales

| Método | URL | Descripción |
|--------|-----|-------------|
| `GET` | `/api/pokemon` | Lista todos los Pokémon |
| `GET` | `/api/pokemon/{id}` | Obtiene un Pokémon por ID |
| `POST` | `/api/pokemon` | Crea un nuevo Pokémon |

> **💡 Tip**: Utiliza Swagger UI para probar todos los endpoints de forma interactiva sin necesidad de herramientas adicionales como Postman.

### `GET /api/pokemon`
Lista todos los Pokémon

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Pikachu",
      "type": "Grass",
      "hp": 80,
      "status": "wild"
    }
  ],
  "total": 1
}
```

### `GET /api/pokemon/{id}`
Obtiene un Pokémon específico por ID

**Respuesta exitosa:**
```json
{
  "data": {
      "id": 1,
      "name": "Pikachu",
      "type": "Grass",
      "hp": 80,
      "status": "wild"
    }
}
```

**Error 404:**
```json
{
  "error": "Pokemon not found",
  "message": "Pokemon with ID 999 not found"
}
```

### `POST /api/pokemon`
Crea un nuevo Pokémon

**Request:**
```json
{
  "name": "Charizard",
  "type": "Fire",
  "hp": 78,
  "status": "wild"
}
```

**Respuesta (201):**
```json
{
  "data": {
    "id": 1,
    "name": "Charizard",
    "type": "Fire",
    "hp": 78,
    "status": "wild"
  }
}
```

## 🛠️ Setup del Proyecto

### Requisitos
- **Docker** instalados
- Git

> **Nota importante**: Este proyecto está completamente dockerizado. Solo necesitas Docker instalado, no requiere PHP, Composer ni SQLite en tu máquina local.

### 🚀 Instalación y Ejecución (Recomendado)

1. **Clonar el repositorio:**
```bash
git clone <repository-url>
cd pokedex_api
```

2. **Levantar el proyecto con Docker:**
```bash
make up
```

Este comando automáticamente:
- ✅ Construye las imágenes Docker
- ✅ Instala dependencias de Laravel/Composer
- ✅ Configura la base de datos SQLite
- ✅ Ejecuta migraciones
- ✅ Carga datos de ejemplo (seeders)

3. **¡Listo!** La API estará disponible en:
   - **API**: `http://localhost:8080`
   - **Documentación Swagger**: `http://localhost:8080/api/documentation`

### 🐳 Comandos Docker Disponibles

```bash
# Levantar contenedores
make up

# Parar contenedores  
make down

# Acceder al contenedor de PHP
make sh

# Ejecutar tests
make test

# Formatear código (Laravel Pint)
make fmt

# Generar documentación Swagger
make swagger

# Artisan Optimize
make optimize

```

## 🏃‍♂️ Formas de Ejecutar el Proyecto

### 🎯 Opción 1: Docker (Recomendado)
```bash
make up
```
**API disponible en**: `http://localhost:8080`

### 🔧 Opción 2: Desarrollo Local
Si prefieres ejecutar sin Docker:

```bash
cd src
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
**API disponible en**: `http://localhost:8000`

### ⚡ Validación Rápida

Para verificar que todo funciona correctamente:

```bash
# 1. Levantar el proyecto
make up

# 2. Verificar que la API responde
curl http://localhost:8080/api/pokemon

# 3. Acceder a la documentación Swagger
# Abrir en navegador: http://localhost:8080/api/documentation
```

### 🔧 Solución de Problemas

**Puerto 8080 ocupado:**
```bash
# Cambiar puerto en docker-compose.yml línea 10:
ports:
  - "8081:80"  # Usar puerto 8081 en lugar de 8080
```

**Error de permisos (Linux/macOS):**
```bash
# Dar permisos al directorio storage
chmod -R 775 src/storage
chmod -R 775 src/bootstrap/cache
```

**Recrear desde cero:**
```bash
make down
docker system prune -f
make up
```

### Ejecutar todos los tests
```bash
# Con Docker (recomendado)
make test

# Desarrollo local
cd src
php artisan test
```
```bash
cd src
php artisan test
```

### Ejecutar tests específicos
```bash
# Tests unitarios
php artisan test --testsuite=Unit

# Tests de integración
php artisan test --testsuite=Feature

# Test específico
php artisan test --filter=PokemonTest
```

### Cobertura de tests
- **Tests unitarios**: Value Objects, Entidades, Handlers con mocks
- **Tests de integración**: Repositorio Eloquent, migraciones
- **Tests E2E**: Endpoints de la API, validaciones, casos de error

## 🔧 Comandos Útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed --class=PokemonSeeder

# Limpiar y recrear BD
php artisan migrate:refresh --seed

# Ver rutas disponibles
php artisan route:list --path=api

# Formatear código
vendor/bin/pint --dirty
```

## 📝 Ejemplos de Uso

### Crear un Pokémon
```bash
curl -X POST http://localhost:8000/api/pokemon \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Blastoise",
    "type": "Water",
    "hp": 79,
    "status": "wild"
  }'
```

### Listar todos los Pokémon
```bash
curl http://localhost:8000/api/pokemon
```

### Obtener un Pokémon específico
```bash
curl http://localhost:8000/api/pokemon/{id}
```

## 🤖 Uso de IA Documentado

Todos los prompts y decisiones tomadas con IA están documentadas en [PROMPTS.md](PROMPTS.md).

## 🏆 Criterios de Calidad Implementados

- ✅ **Clean Architecture**: Dominio independiente, capas bien separadas
- ✅ **Clean Code**: Métodos pequeños, nombres descriptivos, responsabilidades claras
- ✅ **Testing**: 30+ tests unitarios, integración y E2E
- ✅ **Domain-Driven Design**: Value Objects, Entidades, Eventos de dominio
- ✅ **CQS**: Separación clara entre Commands y Queries
- ✅ **Validaciones**: En múltiples capas (dominio, aplicación, HTTP)
- ✅ **Manejo de errores**: Excepciones específicas del dominio
- ✅ **Eventos de dominio**: Para captura de Pokémon

## 📁 Estructura de Archivos

```
├── src/
│   ├── app/Pokemon/           # Módulo Pokemon
│   ├── database/
│   │   ├── migrations/        # Migración de tabla pokemon
│   │   ├── factories/         # Factory para testing
│   │   └── seeders/          # Seeder con datos de ejemplo
│   ├── routes/api.php         # Rutas de la API
│   ├── tests/                 # Tests unitarios y de integración
│   └── ...
├── docker/                    # Configuración Docker
├── Makefile                   # Comandos para Docker
└── README.md                  # Esta documentación
```

---

**Desarrollado con ❤️ siguiendo principios de Clean Architecture y Clean Code**