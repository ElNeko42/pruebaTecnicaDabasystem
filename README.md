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

## Ejecutar los tests

```bash
php artisan test

php artisan test --filter=RoomTypeIsFeaturedTest
```

Los tests usan `RefreshDatabase` sobre SQLite, no requieren ningún servicio externo.

`tests/Feature/RoomTypeIsFeaturedTest.php` cubre los tres casos pedidos:

1. `test_guests_cannot_create_or_update_room_types` — un usuario no autenticado recibe 401 en `POST`/`PUT` y no se modifica nada en base de datos.
2. `test_is_featured_is_persisted_on_create_and_update` — el alta y la edición persisten `is_featured` y lo devuelven en la respuesta.
3. `test_public_endpoint_exposes_a_real_boolean_and_no_internal_fields` — la API pública devuelve exactamente `id`, `name`, `is_featured` y el valor es un booleano real.

## Resumen de los cambios

**Base de datos**

- `database/migrations/2026_09_09_091911_add_is_featured_to_room_types_table.php`: añade `boolean is_featured` con `default(false)`; el `down()` hace `dropColumn`, así que la migración es reversible.
- `RoomTypeFactory` y `DatabaseSeeder` contemplan el campo (en el seeder, "Suite" queda destacada como dato de ejemplo).

**Backend**

- `app/Models/RoomType.php`: `is_featured` en `$fillable` y en `$casts` como `boolean`.
- `app/Http/Requests/RoomTypeRequest.php`: regla `'is_featured' => ['sometimes', 'boolean']`. Además se corrigió `authorize()` para pasar `RoomType::class` a la policy en el alta (`$this->route('room_type')` es `null` en `store`, con lo que la comprobación de permiso no se evaluaba correctamente). Autenticación Sanctum, policy y permisos se mantienen intactos.
- `app/Http/Resources/RoomTypeResource.php`: expone `is_featured` con cast explícito `(bool)`. Sigue sin exponer `created_at`, `updated_at` ni ningún otro campo interno, y es el mismo recurso que usan el panel y la API pública.
- Controlador, acción (`SaveRoomType`) y repositorio (`RoomTypeRepository`) no se han tocado: el campo viaja por el recorrido existente vía `$request->validated()`.

**Panel Vue**

- `resources/js/pages/RoomTypes.vue`: interruptor `v-switch` "Destacado" en el diálogo de alta/edición, columna "Destacado" en el listado (icono estrella) y el `v-alert` de error movido dentro del diálogo, que es donde el usuario ve el fallo de validación. El botón Guardar usa `:loading`/`:disabled` con `store.saving`.
- `resources/js/stores/roomTypes.js`: el manejo de error agrega **todos** los mensajes de `errors` del backend (antes solo leía `errors.name`), con fallback a `message` y a un texto genérico. No se usa `console.log`.
- `resources/js/main.js`: registro explícito de `components`/`directives` de Vuetify, necesario para que `v-switch` y `v-data-table` se resuelvan.

**API pública**

- `GET /api/front/room-types` ya existía (`RoomTypeController::publicIndex`, fuera del grupo `auth:sanctum`). Se ha adaptado su salida al incluir `is_featured` en el recurso; no requiere credenciales.

## Recorrido

**Panel → base de datos**

El formulario de `RoomTypes.vue` mantiene un objeto reactivo con `name` e `is_featured` y llama a `store.save(form)`. El store Pinia hace `POST /api/admin/room-types` o `PUT /api/admin/room-types/{id}` con Axios (`baseURL: /api` y el Bearer token de Sanctum en la cabecera). La ruta está dentro del grupo `auth:sanctum`, así que sin token la petición muere en 401. Ya en el controlador, `RoomTypeRequest` resuelve primero `authorize()` contra `RoomTypePolicy` (`create`/`update`) y después las reglas de validación, incluida `is_featured => boolean`. El controlador pasa `validated()` a `SaveRoomType`, que delega en `RoomTypeRepository` y este hace `create`/`update` sobre el modelo; el cast booleano guarda 0/1 en SQLite. La respuesta vuelve como `RoomTypeResource`, y el store sustituye o inserta el elemento en `items`, de modo que el listado refleja el cambio sin recargar.

**Base de datos → frontend Node**

`GET /api/front/room-types` entra por una ruta pública, sin middleware de auth. El controlador pide `RoomTypeRepository::all()` (ordenado por nombre) y envuelve la colección en `RoomTypeResource`, que proyecta únicamente `id`, `name` e `is_featured`, este último con cast explícito a booleano. El JSON resultante es `{ "data": [ { "id": 12, "name": "Suite", "is_featured": true } ] }` — sin timestamps ni campos de auditoría. El frontend Node consume ese endpoint con `Accept: application/json` y filtra por `is_featured` para pintar los destacados.

## Ejemplo TypeScript para Node

```ts
type RoomType = { id: number; name: string; is_featured: boolean };

export async function fetchRoomTypes(
  baseUrl = 'http://localhost:8000',
): Promise<RoomType[]> {
  const response = await fetch(`${baseUrl}/api/front/room-types`, {
    headers: { Accept: 'application/json' },
    signal: AbortSignal.timeout(5000),
  });

  if (!response.ok) {
    throw new Error(`room-types respondió ${response.status} ${response.statusText}`);
  }

  const body = (await response.json()) as { data?: RoomType[] };
  if (!Array.isArray(body.data)) {
    throw new Error('Respuesta inesperada: falta el array "data"');
  }

  return body.data;
}

// Uso: los errores se degradan sin romper la página pública.
try {
  const featured = (await fetchRoomTypes()).filter((type) => type.is_featured);
  console.info(`${featured.length} tipos destacados`);
} catch (error) {
  console.error('No se pudieron cargar los tipos de habitación', error);
  // render de fallback / lista vacía
}
```

## Comprobaciones manuales realizadas

1. `php artisan migrate` y `php artisan migrate:rollback` sobre la nueva migración: la columna se crea y se elimina sin errores.
2. Login en el panel con `admin@example.test` / `password`.
3. **Alta**: "Nuevo tipo" → nombre + interruptor "Destacado" activado → Guardar. El botón queda deshabilitado con spinner mientras se procesa y el registro aparece en el listado con la estrella rellena.
4. **Edición**: "Editar" sobre ese registro → el interruptor llega marcado con el valor guardado → se desactiva → Guardar. Tras recargar (F5) el cambio persiste.
5. **Error de validación**: guardar con el nombre vacío muestra el mensaje del backend dentro del diálogo, sin trazas en consola.
6. `curl -H "Accept: application/json" http://localhost:8000/api/front/room-types` sin token: responde 200 con `id`, `name` e `is_featured` booleano y ningún timestamp.
7. `php artisan test` en verde.

## Trabajo pendiente

- Los textos del panel siguen escritos en literal (el proyecto base no incluye i18n en el panel); lo correcto sería sacar "Destacado", "Nuevo tipo", etc. a un fichero de traducciones y a `lang/` los mensajes de validación.
- Sin tests de frontend (Vitest + Testing Library) para el switch y el manejo de errores del store; no eran obligatorios.
- Filtro/orden por "destacado" en el listado del panel y parámetro `?featured=1` en la API pública.
- El endpoint público no tiene caché ni paginación; con volumen alto convendría añadir ambas.

## Cambios más complejos

Para un **campo único** añadiría el índice único en la migración y una regla `Rule::unique('room_types','name')->ignore($this->route('room_type'))`, apoyada por un test de duplicado, para que el error llegue como 422 y no como excepción de base de datos. Para **traducciones** movería el campo a una tabla `room_type_translations` (o una columna JSON con `spatie/laravel-translatable`), con el recurso resolviendo el idioma del `Accept-Language` y fallback al idioma por defecto. Para **relaciones entre sitios** añadiría `site_id` con un global scope por sitio activo, aplicándolo también en la policy para que un administrador no pueda tocar registros de otro sitio. Para **compatibilidad de API** versionaría el endpoint público (`/api/front/v2/...`) o añadiría campos siempre de forma aditiva, nunca renombrando ni eliminando los existentes, dejando los deprecados marcados con fecha de retirada.
