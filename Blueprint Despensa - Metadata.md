---
sistema: Despensa
tipo: metadata-blueprint
---
# Metadata — Blueprint Despensa

## Registro de sesiones

### 2026-11-16 — creación — German y agente

**Instrucciones del usuario:**

- Generar un plan funcional con casos de uso para una versión multi-comercio y autoservicio de Despensa, a partir del repositorio actual.
- Varios usuarios: cada comercio se registra solo y un administrador de la plataforma controla todo por encima.
- En el primer ingreso después del registro, el comercio elige su nombre, una imagen y uno de los temas preseleccionados.
- Diseño mobile-first, con aspecto de aplicación, pensado para personas mayores.
- Catálogo por defecto según el rubro: verdulería, ferretería, almacén y carnicería.
- Control de stock y alta de clientes.
- Fiado: la venta fiada conserva los productos comprados y calcula el total adeudado; se comparte por WhatsApp como link público que resume lo pagado y lo que se debe, con total y pagos restados (fecha, monto y medio de pago).
- Sólo contenido funcional: sin archivos, clases, endpoints, tablas ni referencias de código; todo nace 🟡 preliminar.
- Formato Prometeo, en `docs/blueprint/`, con el nombre **Despensa**.

**Fuentes utilizadas:**

| Fuente | Tipo | Ubicación | Qué aportó |
| --- | --- | --- | --- |
| Definición funcional conversada con el usuario | documental | sesión del 2026-11-16 | alcance, multi-comercio, alta autoservicio, onboarding, catálogo por rubro, fiado compartido y diseño para personas mayores |
| Documentación funcional del proyecto actual | documental | `proyect.md` | módulos, roles, fiado, stock, temas, reglas de dinero y cantidades y esquema de datos vigente |
| Código del repositorio | código | `C:\Users\German\Documents\despensa-basica`, rama `main` | comportamiento real de acceso, catálogo, ventas, fiado, gastos y configuración; base para el documento de drift |
| Blueprint Prometeo | documental | `~/Downloads/Blueprint Prometeo.md` | gramática, niveles y wikilinks de cada tipo de nodo |
| Blueprint Template | documental | `~/Documents/pet-manager/docs/blueprint/Blueprint Template.md` | secciones exactas de cada nodo |
| Blueprint Pet Manager | documental | `~/Documents/pet-manager/docs/blueprint/Blueprint Pet Manager.md` | ejemplo de tono, numeración y nivel de detalle |

**Nodos creados o modificados:** raíz `# Descripción general` · `role-001` a `role-004` · `domain-001` a `domain-025` · `process-001` a `process-002` · `rule-001` a `rule-044` · `ui-001` a `ui-019` · `scenario-001` a `scenario-025` · `use-case-001` a `use-case-057` · `note-001` a `note-003` · módulos `ACC`, `ONB`, `CAT`, `CLI`, `VEN`, `FIA`, `GAS`, `CFG` y `PLA`.

**Nodos confiables tocados (con confirmación):** ninguno

**Supuestos asumidos:**

- (2026-11-16) Una cuenta de acceso pertenece a un solo comercio; quien tiene dos locales usa dos cuentas con dos correos.
- (2026-11-16) Un comercio opera en el acto después de registrarse; la plataforma lo supervisa y puede suspenderlo después, sin aprobación previa.
- (2026-11-16) El cliente del fiado no tiene cuenta: el resumen se abre con un link público, protegido por un código largo y difícil de adivinar.
- (2026-11-16) El primer ingreso es obligatorio y se hace una sola vez; después, nombre, imagen, tema y rubro se cambian desde Configuración.
- (2026-11-16) El catálogo del rubro se copia sin precios y con existencia en cero.
- (2026-11-16) Cambiar de rubro no borra el catálogo: sólo ofrece agregar lo que falte.
- (2026-11-16) El vendedor no ve el total de gastos del comercio.
- (2026-11-16) Una venta tiene una sola forma de pago; no se admiten pagos divididos en varios medios.
- (2026-11-16) La anulación de una venta es nueva y sólo la puede hacer el dueño, el mismo día, con motivo.
- (2026-11-16) El resumen del fiado sigue abriendo aunque el comercio esté suspendido.
- (2026-11-16) El administrador de la plataforma ve cantidades y montos agregados, nunca nombres, teléfonos ni deudas de clientes.
- (2026-11-16) La recuperación de contraseña depende de un envío de correo que hoy la plataforma no tiene; queda como requisito, no como comportamiento existente.
- (2026-11-16) El abono puede superar la deuda y deja saldo a favor; no hay imputación a compras puntuales.
- (2026-11-16) El vendedor puede crear productos, cambiar precios y ajustar existencia; lo que no puede es tocar gastos, configuración ni personal.
- (2026-11-16) La recuperación de contraseña se implementa con link por email; el caso de uso existe en el Blueprint y se construye cuando haya infraestructura SMTP.
- (2026-11-16) El resumen público del fiado muestra el detalle completo de cada compra: productos, cantidades y precios (confirmado en `rule-031` y `ui-013`).
- (2026-11-16) El link del resumen del fiado no expira automáticamente; vive hasta que el dueño lo renueva con `use-case-037`.
- (2026-11-16) Un abono mal cargado se anula por baja lógica con motivo obligatorio (`use-case-035b`); solo el dueño puede anularlo; el registro queda visible.
- (2026-11-16) Un dueño con dos sucursales debe usar dos cuentas con dos correos distintos; no hay soporte multi-sucursal en esta versión (decisión pendiente de revisión futura).

**Decisiones resueltas (2026-11-16):**

- **Recuperación de contraseña (`use-case-003`):** se implementa con link por email (SMTP). El caso de uso queda en el Blueprint pero se construye en una fase posterior cuando haya infraestructura de email. No se elimina del alcance.
- **Detalle del resumen público del fiado (`ui-013`, `rule-031`):** muestra el detalle completo de cada compra (productos, cantidades y precios). Confirmado tal como está en el Blueprint.
- **Expiración del link del fiado:** el link no expira automáticamente; vive hasta que el dueño lo renueva manualmente con `use-case-037`. Sin job periódico ni lógica de expiración.
- **Anulación de abono mal cargado:** se agrega un caso de uso de baja lógica: el dueño puede anular un abono con motivo obligatorio; el abono anulado deja de sumar en el saldo pero sigue visible en el historial. Solo el dueño puede anularlo.
- **Un dueño, dos sucursales:** queda abierto. No se define en esta versión; si aparece la necesidad, se revisa el modelo de datos antes de implementar.

**Huecos que quedaron abiertos:**

- Límite de tamaño y formatos aceptados para la imagen del comercio.
- Cómo se evita duplicar clientes cargados dos veces con nombres parecidos.
- Qué pasa si un comercio quiere dos sucursales con un mismo dueño (pendiente de decisión).
- Si hace falta avisar por correo o por la aplicación cuando la plataforma suspende un comercio.
- No hay reportes de ventas por período, ranking de productos ni cierre de caja: quedan afuera de esta versión.
- No hay exportación de datos, impresión de comprobantes ni funcionamiento sin conexión.
- No hay planes, pagos ni facturación de la plataforma a los comercios.
- No se define qué pasa con las ventas y deudas de un comercio que se da de baja definitivamente.

**Drift no aplicado (rechazado por el usuario):** ninguno. Las diferencias entre este blueprint y el código actual se registran en `Blueprint Despensa - Drift.md`; ninguna se resolvió en silencio dentro del blueprint.

**Migración:** no aplica. El blueprint describe el sistema completo en su versión multi-comercio; el estado del código y el orden de construcción sugerido están en el documento de drift.
