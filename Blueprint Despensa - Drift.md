---
sistema: Despensa
tipo: drift-blueprint
---
# Drift — Blueprint Despensa frente al código actual

**Qué es este documento.** Contrasta el blueprint con lo que hoy hace el repositorio. El blueprint describe la versión multi-comercio y autoservicio; el código es un panel de un solo comercio con login de administradores. Nada de lo que falta se escribió en el blueprint como si existiera: se documentó como alcance, y acá queda el detalle de la diferencia, del esfuerzo y del orden posible.

## Resumen

| | Código actual | Blueprint |
| --- | --- | --- |
| Comercios | Uno solo, implícito en la base | Muchos, aislados entre sí (`rule-001`) |
| Altas | Administrador semilla, sin registro | Registro y onboarding autoservicio (`use-case-004`, `009`–`012`) |
| Roles | Todos los administradores son iguales | Dueño y vendedor con permisos distintos (`role-002`, `rule-009`) |
| Fiado | Cuenta corriente interna | Cuenta corriente + resumen público por WhatsApp (`domain-017`) |
| Plataforma | No existe | Panel de supervisión y suspensión (`Módulo PLA`) |

Lo reutilizable de verdad es la mecánica del negocio: catálogo con precio opcional, venta con foto de precios, descuento de stock dentro de una transacción, libro de movimientos con signo, gastos y configuración tipada. Lo que falta es estructural: el límite entre comercios, el alta y los roles, el link público del fiado y el panel de la plataforma.

## Estado por módulo

| Módulo | Qué hay hoy | Qué pide el blueprint | Estado |
| --- | --- | --- | --- |
| `ACC` | Login con JWT, ABM de administradores, tema por administrador. Sin registro, sin roles, sin recuperación de contraseña | Registro, dueño y vendedor, personal, recuperación | Parcial: el login sirve, el resto es nuevo |
| `ONB` | Nada | Asistente de cuatro pasos y catálogo por rubro | Nuevo |
| `CAT` | Categorías y productos con nombre único, precio opcional, existencia en milésimas, descuento al vender | Catálogo por rubro, historial de existencia con motivo, imagen | Parcial: falta el rubro, el historial y la imagen |
| `CLI` | ABM con búsqueda, apodo, teléfono, calificación y comentario | La misma ficha, atada a un comercio | Cubre la ficha; falta el aislamiento |
| `VEN` | Venta con renglones, precio congelado, total calculado, un medio de pago, stock descontado en transacción | Lo mismo, más anulación con motivo y responsable de la venta | Parcial: faltan anulación y «quién cobró» |
| `FIA` | Movimientos con signo, compras y abonos, saldo que puede quedar a favor, deudores | Lo mismo, más link público, compartir por WhatsApp y renovar el link | Parcial: la cuenta está; el resumen compartido no |
| `GAS` | Tipos de gasto, gastos con comprobante, filtros, medios sin fiado | Igual | Cubre casi todo |
| `CFG` | Settings tipados con claves protegidas, branding público, siete temas y tema por cuenta | Igual, más la identidad del comercio y el cambio de rubro | Cubre casi todo |
| `PLA` | Nada | Listado, actividad agregada, suspensión, rubros y plantillas | Nuevo |

## Drift detallado

1. **Aislamiento entre comercios** (`rule-001`, `rule-002`). Ninguna tabla sabe a qué comercio pertenece un dato y ninguna consulta filtra por comercio. Es el cambio más grande: toca todas las tablas, todos los listados y todos los accesos. Sin esto, sumar un segundo comercio mezcla clientes, precios y deudas.
2. **Registro y onboarding** (`use-case-004`, `009`–`012`). Hoy el administrador se crea por semilla; no hay pantalla de registro ni asistente. El onboarding además necesita los rubros y sus catálogos, que tampoco existen.
3. **Roles y personal** (`rule-009`, `use-case-005`–`007`). No hay campo de rol: cualquier administrador puede todo. La regla del último dueño activo y la baja lógica del vendedor son nuevas.
4. **Recuperación de contraseña** (`use-case-003`). No hay envío de correo en la plataforma. El caso está documentado como requisito; si no se resuelve el envío, hay que definir una alternativa (por ejemplo, que el dueño restablezca la contraseña de un vendedor desde Personal).
5. **Rubros y catálogo inicial** (`domain-002`, `domain-024`, `rule-043`). No hay plantillas de rubro. El blueprint asume cuatro y un catálogo modelo editable por la plataforma; hoy habría que cargarlos como datos iniciales.
6. **Historial de existencia** (`rule-017`, `use-case-018`). El código guarda sólo el número final: no hay registro de ajustes con motivo, fecha y responsable. El ajuste manual existe como cambio de valor, no como movimiento explicado.
7. **Imagen del comercio** (`use-case-010`, `048`). El logo es hoy una ruta o URL escrita a mano en un setting; no hay subida de archivos ni vista previa recortada.
8. **Anulación de venta** (`rule-022`, `use-case-031`). La venta no tiene estado ni forma de anularse: se guarda y queda. Falta el estado, el motivo, la devolución de stock y la compensación de la cuenta corriente.
9. **Responsable de la venta** (`rule-023`). La venta no guarda qué cuenta la cobró. Es un campo y un dato más en el listado, pero es requisito de la regla.
10. **Resumen público del fiado** (`domain-017`, `use-case-036`–`038`, `rule-031`–`034`). No existe: ni link, ni página de sólo lectura, ni código renovable, ni compartir por WhatsApp. Es la función que más cambia la experiencia del cliente y la que más trabajo nuevo pide después del aislamiento.
11. **Panel de plataforma** (`Módulo PLA`). No hay concepto de plataforma ni de comercio supervisado. Suspender un comercio, reactivarlo y ver actividad agregada son desarrollos nuevos.
12. **Restricciones del vendedor** (`role-002`). Sin roles no hay restricciones: hoy quien entra puede tocar gastos, configuración y personal. Depende del punto 3.
13. **Vuelto en efectivo** (`use-case-027`). El cobro no calcula ni muestra el vuelto. Es una mejora de pantalla, sin cambio de datos.
14. **Datos de la plataforma sobre los clientes** (`rule-042`). No hay panel, así que tampoco hay riesgo hoy; la regla queda escrita para que el panel nuevo no exponga nombres ni deudas.

## Lo que ya sirve tal cual

- **Dinero y cantidades:** centavos enteros y milésimas, con el total de renglón calculado por proporción. El blueprint usa la misma convención sin nombrarla.
- **Precio opcional:** el producto puede quedar «sin precio» y la venta lo rechaza; coincide con `rule-013`.
- **Foto del renglón:** `sale_items` guarda nombre, marca y precio del momento; coincide con `rule-024`.
- **Stock en la transacción:** la venta valida y descuenta existencia dentro de la misma operación; coincide con `rule-026` y con la primera mitad de `rule-016`.
- **Cuenta corriente con signo:** los movimientos suman y restan, el saldo es la suma y puede quedar a favor; coincide con `rule-027`, `rule-029` y `rule-030`.
- **Baja lógica:** clientes, productos y administradores se desactivan; coincide con la idea de conservar el historial.
- **Configuración tipada y claves protegidas:** el nombre, el tema y el logo no se borran; coincide con `domain-021` y con el espíritu de `rule-040`.
- **Siete temas y tema por cuenta:** ya están los siete y la preferencia personal; coincide con `rule-041`.
- **Gastos:** tipos con nombre único, medios de pago sin fiado, filtros y comprobante; coincide con casi todo el módulo `GAS`.

## Orden de construcción sugerido

**Fase 0 — preparar la base.** Definir el comercio como dueño de todos los datos y migrar lo que hoy existe a un primer comercio, para no perder el catálogo, los clientes ni las deudas actuales. Sin esta fase, cualquier avance posterior deja datos huérfanos.

**Fase 1 — acceso y aislamiento.** Comercio en todas las tablas y en todas las consultas, registro, roles de dueño y vendedor, Personal, y el comercio viajando con la sesión. Es la fase que habilita todo lo demás.

**Fase 2 — alta del comercio.** Onboarding de cuatro pasos, rubros con su catálogo modelo y copia del catálogo al crear el comercio. Incluye la imagen del comercio y el cambio de rubro desde Configuración.

**Fase 3 — catálogo y stock completos.** Historial de existencia con motivo y responsable, reglas de precio y de producto inactivo, y las restricciones del vendedor sobre precios y gastos.

**Fase 4 — venta completa.** Responsable de la venta, vuelto en efectivo, anulación con motivo y compensación de stock y deuda. Es poco volumen y cierra los agujeros del mostrador.

**Fase 5 — fiado compartido.** Código público por cuenta, página de sólo lectura, compartir por WhatsApp, renovación del link y respeto de la privacidad entre cuentas. Es la función que justifica el producto para el cliente final.

**Fase 6 — panel de plataforma.** Listado de comercios, actividad agregada sin datos de clientes, suspensión y reactivación, y mantenimiento de rubros y plantillas. Puede convivir con las fases anteriores si se cargan los rubros por datos iniciales.

**Fase 7 — después.** Reportes por período, ranking de productos, cierre de caja, exportación y notificaciones. Nada de esto está en el blueprint y no debería frenar las fases anteriores.

## Riesgos y cuidados

- **La migración de datos actuales es irreversible si se hace mal.** Antes de tocar el esquema hay que respaldar la base y probar la migración sobre una copia.
- **Un filtro olvidado por comercio es una fuga de datos.** Conviene que el aislamiento se aplique en un solo lugar y no consulta por consulta.
- **El link público no puede ser adivinable ni reutilizable entre cuentas.** Tiene que ser un código largo, propio de cada cuenta, revocable, y la página no debe ofrecer navegación hacia otros datos.
- **La recuperación de contraseña depende de infraestructura que hoy no existe.** Si no se resuelve el envío de correo, hay que acordar una alternativa antes de prometer el caso de uso.
- **Suspender un comercio no puede romper el resumen del cliente.** La regla está escrita, pero el panel nuevo tiene que respetarla desde el primer día.
