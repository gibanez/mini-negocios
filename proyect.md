# Proyecto — documentación específica

> **Qué es este archivo.** Documenta **este proyecto en particular**: qué módulos tiene, qué decisiones propias tomó y cómo se despliega. Se completa/edita en cada fork.
> `AGENTS.md` contiene las convenciones **genéricas** del stack (iguales en todos los proyectos): no hace falta tocarlo acá.
> OpenCode carga este archivo automáticamente porque está listado en `opencode.jsonc` (`instructions`).
> Si una decisión de este proyecto contradice a `AGENTS.md`, gana este archivo (se carga después).

## Identidad

- **Nombre:** Despensa
- **Descripción:** Plataforma multi-comercio y autoservicio para almacenes de barrio. Cada comercio se registra solo, elige su identidad visual y opera de forma aislada. Un administrador de plataforma supervisa todos los comercios desde un panel separado.
- **Estado:** diseño completo en Blueprint; código base del template heredado (Auth, Pages, Inquiries); ningún módulo Despensa está implementado aún.
- **Repositorio:** privado (fork de `php-ddd-base`)

## Stack

- **PHP:** 7.4 (`config.platform` en `composer.json`).
- **Base de datos:** SQLite (`storage/database.sqlite`), migrada con Phinx.
- **Frontends:** HTML + CSS + JS sin build; mobile-first, diseñado para personas mayores.
- **Hosting / deploy:** `php -S localhost:9980 -t public` para desarrollo; Apache con `public/.htaccess` para producción.

## Modelo multi-tenant

Cada comercio (`tenant`) es el dueño de todos sus datos: productos, clientes, ventas, fiado, gastos y configuración. Ninguna consulta devuelve datos de otro comercio. El aislamiento se aplica en un solo punto (middleware / repositorio base) y no consulta por consulta.

Cada sesión viaja con el `store_id` del comercio autenticado. El administrador de plataforma tiene su propia cuenta sin `store_id`.

## Módulos

| Código | Nombre | Propósito | Estado |
| --- | --- | --- | --- |
| `ACC` | Acceso | Registro, login, roles (dueño y vendedor), personal, recuperación de contraseña | No iniciado |
| `ONB` | Onboarding | Asistente de primer ingreso: nombre, imagen, tema y rubro | No iniciado |
| `CAT` | Catálogo | Productos, categorías, precios opcionales, existencia, historial de ajustes | No iniciado |
| `CLI` | Clientes | Ficha del cliente: nombre, apodo, teléfono, calificación, estado | No iniciado |
| `VEN` | Ventas | Carrito, cobro, formas de pago, anulación con motivo, responsable | No iniciado |
| `FIA` | Fiado | Cuenta corriente, abonos, resumen público por WhatsApp, link renovable | No iniciado |
| `GAS` | Gastos | Tipos de gasto, registro con comprobante, filtros por período | No iniciado |
| `CFG` | Configuración | Identidad del comercio, rubro, temas (comercio y personal) | No iniciado |
| `PLA` | Plataforma | Panel de supervisión, suspensión/reactivación, rubros y plantillas | No iniciado |

Rutas de salud: `GET /api/ping` (heredado del template).

## Autenticación y roles

- `POST /api/auth/login` recibe `{ email, password }` y devuelve `{ token, user }`.
- El token JWT incluye `sub` (id del usuario), `store_id` y `role` (`owner` o `vendor`).
- El administrador de plataforma no tiene `store_id` en el token; su rol es `platform_admin`.
- `JwtAuthMiddleware` protege todas las rutas de escritura.
- Regla del último dueño activo: no se puede desactivar ni degradar al único dueño de un comercio.

## Orden de construcción

Ver `Blueprint Despensa - Drift.md` → sección "Orden de construcción sugerido".

Resumen: Fase 0 (aislamiento de datos) → Fase 1 (acceso y roles) → Fase 2 (onboarding) → Fase 3 (catálogo y stock) → Fase 4 (venta completa) → Fase 5 (fiado compartido) → Fase 6 (panel de plataforma).

## Decisiones abiertas (bloqueantes antes de escribir código)

Las siguientes decisiones deben resolverse antes de implementar los módulos que las requieren. Ver `Blueprint Despensa - Metadata.md` para el detalle.

1. **Recuperación de contraseña** (`use-case-003`): sin infraestructura SMTP hoy. Opciones: a) link por email, b) el dueño resetea la contraseña del vendedor desde Personal, c) fuera del scope inicial.
2. **Resumen público del fiado** (`use-case-038`): ¿muestra el detalle de productos por compra o solo el total por compra? (El Blueprint documenta detalle completo en `rule-031` y `ui-013`; confirmar o ajustar.)
3. **Expiración del link de fiado**: ¿el link vive mientras esté activo (sin vencimiento automático) o expira después de N días?
4. **Anulación de abono mal cargado**: ¿quién puede anularlo y cómo se registra (movimiento inverso o baja lógica)?
5. **Un dueño, dos sucursales**: ¿se admite o se documenta que cada sucursal requiere una cuenta separada con otro correo?

## Base de datos y migraciones

- Esquema completo definido en `database/migrations/`, una migración por tabla/cambio.
- Ningún repositorio crea tablas.
- Tabla `stores` como raíz del tenant; todas las demás tablas tienen `store_id` como FK.
- UUIDs en todas las tablas (VARCHAR 36).
- Comandos: `vendor/bin/phinx migrate | rollback | status`.

## Decisiones y patrones propios

- Catálogo por rubro: las plantillas viven en la tabla `industry_templates` y se copian (sin precio, existencia en cero) al crear el comercio.
- Cambiar de rubro no borra el catálogo existente; solo ofrece agregar lo que falta.
- El vendedor no puede tocar gastos, configuración ni personal.
- Venta con un solo medio de pago; no se admiten pagos divididos.
- Anulación de venta: solo el dueño, el mismo día, con motivo obligatorio.
- Link del fiado: código UUID largo por cliente, renovable por el dueño; no expira automáticamente (pendiente decisión 3).
- Montos en centavos enteros; cantidades en milésimas (igual que el template base).
- El resumen del fiado sigue abriendo aunque el comercio esté suspendido (`rule-034`).

## Deploy

1. `composer install --no-dev --optimize-autoloader`.
2. Apuntar el document root a `public/` (o usar el `.htaccess` raíz descripto en `AGENTS.md`).
3. Generar `.env` con `JWT_SECRET`, `APP_DEBUG=false`, `APP_TIMEZONE`, `DB_PATH`.
4. Subir `storage/database.sqlite` ya migrado (o correr `phinx migrate` en el server).

## Gotchas

- `lcobucci/jwt 4.3`: `ValidAt` recibe un `Lcobucci\Clock\Clock`; usar `new ValidAt(SystemClock::fromUTC())`.
- En el PHP local falta `ext-zip`: correr `php -d extension=zip C:\composer\composer.phar install` o habilitar en `php.ini`.
- `php -S` no lee `.htaccess`: las rutas funcionan igual por el fallback del servidor embebido.
- `APP_DEBUG=true` muestra stack traces: apagarlo en producción.
- Reiniciar `php -S` al cambiar `definitions.php` o `routes.php`.
- `curl` con tildes en Windows: mandar JSON desde archivo UTF-8 con `--data-binary @archivo.json`.
