# Dashboard y reportes — guía de mantenimiento

Revisión: 31 de octubre de 2026. Proyecto: `C:/xampp/htdocs/aeternus`.

Esta guía describe el código actual de esta integración. No es una auditoría de todos los archivos históricos del proyecto ni certifica la conciliación de la base real. Los comentarios dentro del código se identifican con **DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD** y las notas de métodos con **Mantenimiento:**.

## Dónde comenzar

| Necesito revisar… | Archivo o punto de entrada |
| --- | --- |
| Qué recibe el dashboard y quién puede verlo | `app/Http/Controllers/DashboardController.php` → `buildDashboard` |
| Carga, estados y apertura de reportes | `resources/js/src/views/HomeInfo.vue` |
| Una cantidad de ventas o capital pendiente | `OperacionesController` → `getVentasSaldosOperacionesDashboard` |
| Columnas/datos de una operación particular | `CementerioController` (1/2) o `FunerariaController` (3/4/5) |
| Inventario y último ajuste | `InventarioController` → `getInventarioDashboard`, `getUltimoAjusteInventarioDashboard` |
| Saldo fiscal, facturado/cubierto y timbres | `FacturacionController`, no el motor de capital de operaciones |
| Fechas/estados de seguimientos | `SeguimientosController` |
| Diseño de cards, barra superior, visor o correo | `resources/sass/vuexy/_dashboard.scss` |
| Presentación HTML o PDF de un reporte | Su Blade en `resources/views/reportes/temporales_visor_excel/` |
| Columnas, hojas, logo y formato del XLSX | Su clase en `app/Exports/` |
| Modal genérico del reporte | `resources/js/src/views/pages/VisorReportes.vue` |
| Destinatarios, editor y selección del adjunto | `EnviarReporteCorreo.vue` |
| Remitente, destinos en pruebas y HTML seguro | `EmailController@reporte_email` |
| Apariencia del correo recibido | `resources/views/mails/mail.blade.php` |
| Regularizar saldo/status guardados | Comando `ActualizarSaldosOperaciones`, separado de la carga del dashboard |

## Recorrido de datos

1. `HomeInfo.vue` llama `services/dashboard.js` → `GET /dashboard/build`.
2. `DashboardController` resuelve el usuario con `auth:api`, define `acceso` y obtiene solo los resúmenes permitidos.
3. Las cards reciben props y emiten el reporte seleccionado; no consultan tablas ni deciden permisos por sí mismas.
4. Al abrir un reporte, su servicio llama al método `preview*`. Se valida acceso, se calcula el detalle o conteo y se guarda un token privado en caché.
5. `VisorReportes.vue` muestra HTML dentro de un iframe. Todavía no se ha creado un Excel.
6. Descargar o enviar solicita el archivo al backend; entonces actúan el exportador XLSX o el generador PDF.

El nombre `temporales_visor_excel` es histórico: contiene templates Blade para el visor de reportes HTML/PDF; no es una carpeta de archivos Excel temporales generados.

## Permisos y estado de pantalla

Admin (rol 1) y Gerencia (rol 4) reciben indicadores gerenciales. Los demás perfiles tienen la vista operativa permitida por el backend. No basta con ocultar una card para proteger datos: debe conservarse la validación del servidor.

`successResponse` entrega el dashboard como raíz de `response.data`; no agregar un segundo nivel `data` sin ajustar el consumidor.

La carga usa skeletons. El botón superior de actualización gira durante la consulta; se retiró su capa de carga Vuesax. La barra usa `position: sticky`, queda a la derecha y toma fondo crema cuando alcanza su posición fija. El margen superior compacto es exclusivo de `.dashboard` en `_dashboard.scss`.

La actualización por inactividad está en `HomeInfo.vue`: el temporizador se reinicia con actividad y, tras 5 minutos sin ella, intenta actualizar. Se pospone si hay un reporte abierto o una carga en curso. Conserva el periodo independiente de la gráfica y limpia listeners/temporizador al destruirse el componente.

## Documentos de referencia

- [Catálogo de los 82 archivos documentados](ARCHIVOS.md): propósito, qué ajustar, conexiones y métodos con línea de referencia.
- [Reglas y fuentes de datos](REGLAS.md): alcances, estados, cálculo de saldos, timbres y mantenimiento.
- [Visor, exportaciones y correo](VISOR-Y-REPORTES.md): contrato, límites, caducidad y cómo incorporar reportes.

## Alcance de esta revisión

Se añadieron comentarios y esta guía; no se cambiaron consultas ni comportamiento. Se compararon tokens ejecutables PHP, código JavaScript/Vue, templates Vue, SCSS y Blade compilado contra las copias previas. No se consultó la BD real, no se ejecutó mantenimiento, no se envió correo y no se ejecutó Git.

La paginación del resumen diario depende de la cantidad de servicios/texto. Está diseñado compacto para carta vertical; no se garantiza una sola hoja con cualquier cantidad de registros. La integración automática de consumos futuros de timbres en el flujo de timbrado requiere trabajo específico: el historial inicial y su reporte no la implementan por sí solos.
