# Prueba técnica Laravel + Vue

Proyecto base local y anonimizado para una prueba de máximo 2 horas. Laravel 10, PHP 8.1+, SQLite, Sanctum, Vue 3, Vite, Vuetify, Pinia y Axios.

## Instalación

```bash
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
```

En otra terminal: `php artisan serve`. Abre `http://localhost:8000`.

Credenciales demo: `admin@example.test` / `password`.

## Punto de partida

El panel ya permite iniciar sesión, listar, crear y editar tipos de habitación. El backend sigue el recorrido ruta → FormRequest → policy → acción → repositorio → modelo. La base intencionadamente no contiene `is_featured`.

## Tests

```bash
php artisan test
```

Los tests base comprueban que el entorno y el endpoint público funcionan. Los tres tests específicos de `is_featured` forman parte del ejercicio.

## Consumo desde Node

```ts
type RoomType = { id: number; name: string; is_featured: boolean };

const response = await fetch('http://localhost:8000/api/front/room-types', {
  headers: { Accept: 'application/json' },
});
if (!response.ok) throw new Error(`La API respondió ${response.status}`);
const body = (await response.json()) as { data: RoomType[] };
```

## Comprobación manual

Inicia sesión, crea un tipo, comprueba que aparece en el listado, edítalo y verifica que el cambio se conserva tras recargar.

## Trabajo pendiente

La implementación de `is_featured`, su migración reversible, validación, cast, formulario/listado Vue, adaptación del endpoint público y los tres tests asociados quedan a cargo del candidato. No se incluye frontend público completo.
