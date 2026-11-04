# Visor, exportaciones y correo

## Qué es reutilizable

`VisorReportes.vue` es un contenedor de presentación. Recibe callbacks desde el módulo que lo abre: `cargar`, `descargarArchivo`, `descargarPdf`, `enviarCorreo` y, opcionalmente, `descargarCompleto`. `show` controla apertura; `mostrarVolverResumen` permite volver al concentrado. No conoce tablas ni calcula saldos.

La implementación actual espera HTML para su iframe. No incorpora automáticamente los reportes PDF binarios del visor antiguo `pdf_viewer.vue`: esa migración queda para después. Los Blade existentes pueden reutilizarse adaptando su respuesta y estilos de vista previa.

## Contrato de preview

```text
titulo: título legible
nombreArchivo: nombre del archivo descargable
tokenDescarga: 64 caracteres hexadecimales
venceEn: fecha de caducidad
vistaPreviaDisponible: boolean
limiteVisualizacion: máximo para HTML
totalRegistros: filas consideradas por el reporte
hojas: [{ nombre, html }]
soloPdf: true para el resumen diario
mensaje: explicación si no se puede visualizar
```

Existen metadatos adicionales de navegación para general/cuotas y detalle completo. Revisar los `preview*` y su consumo en `HomeInfo.vue` antes de agregar nuevas opciones. `soloPdf` controla el reporte diario; no asumir que ya existe un catálogo arbitrario de formatos por reporte.

## Apertura, descarga e impresión

Abrir crea HTML y una instantánea o filtros en caché. Descargar solicita el archivo y lo genera bajo demanda. El visor conserva el Blob recibido para evitar repetir la descarga durante esa apertura. Imprimir usa el HTML del iframe, sus reglas `@media print` y el navegador; descargar PDF usa el motor del servidor. Ambos pueden paginar distinto y deben revisarse por separado.

La vista previa admite normalmente 3500 filas; clientes con pagos vencidos admite 5000. Al exceder el límite no se truncan los registros: se ofrece descargar completo. El conteo puede abarcar varias secciones según el reporte. No confundir límite de visualización con límite SQL del reporte descargable.

Reportes grandes/detalles completos pueden guardar solo filtros y reconstruirse al pedir descarga. Aunque se conserva el corte de referencia, las aplicaciones/estados actuales pueden cambiar desde la apertura; no prometer una instantánea inmutable si se usa ese modo diferido.

## Token privado y sesión

Los endpoints están dentro de `auth:api`; los controladores comprueban además rol y propietario del token. El token identifica datos privados de un reporte, no sustituye el token de sesión. Tiene vigencia de 15 minutos. Su vencimiento devuelve 410; el usuario puede actualizar/reabrir el reporte sin cerrar su sesión por esa causa. No convertir la caducidad del reporte en 401.

## Dónde cambiar el formato

| Cambio | Archivo |
| --- | --- |
| Marco del modal, encabezado y acciones | VisorReportes.vue y _dashboard.scss. |
| Posición/campos/colores dentro de preview o PDF | Blade de la categoría del reporte. |
| Tamaño/orientación/márgenes del PDF | Método construirPdf*/pdf* del controlador, además de CSS de impresión. |
| Columnas y hojas del XLSX | Exportador correspondiente. |
| Arial, logo, números y protección de hoja | Clase *Hoja compartida o InventarioExport. |
| Nombre de empresa/dirección | NombreEmpresaReportes y DireccionReportes, más los datos de empresa. |
| Pie PDF, paginación y fecha | compartidos/footer_pdf.blade.php y su integración en el generador. |

La protección de Excel evita ediciones accidentales; no es cifrado ni garantía de confidencialidad. El zoom inicial es fijo (InventarioExport usa 115%), separado del ajuste de impresión: no garantiza ocupar exactamente el ancho de cualquier monitor.

El resumen diario solo PDF usa carta vertical, servicios en dos columnas y una tabla compacta de ventas al imprimir. El contenido variable puede requerir más de una página. La gráfica con selector de mes/año no pertenece a este resumen.

## Envío por correo

`EnviarReporteCorreo.vue` permite buscar por correo o nombre de clientes, agregar/editar destinatarios y confirmar si se reemplazan datos ya escritos. Hasta 20 destinatarios. `vue2-editor` captura el mensaje; límite visible 5000 caracteres y validación adicional del HTML en servidor. El toggle de detalle completo corresponde a Excel cuando el reporte lo admite; el resumen diario solo admite PDF.

El backend del reporte valida campos y obtiene el adjunto; `EmailController@reporte_email` sanea el mensaje con HTMLPurifier y envía. Varios receptores usan copia oculta para no compartir direcciones. En cualquier `APP_ENV` diferente de `production`, sustituye la lista completa por `hectorcrzprz@gmail.com`. Este cambio se aplica al nuevo `reporte_email`; `pdf_email` conserva su circuito anterior. El template `mails/mail.blade.php` sí es compartido por ambos.

Los envíos tienen `throttle:5,1,dashboard-correo:`. Ante 429 no repetir automáticamente: es el límite de solicitudes de correo, no la caducidad del reporte ni un fallo de sesión.

## Cómo incorporar otro reporte

1. Definir alcance, estados, responsable del módulo y datos del resumen/detalle.
2. Crear métodos de consulta que puedan compartir la misma base para indicador y reporte.
3. Crear preview con permisos, conteo/límite, caché privada, título, HTML y token.
4. Reutilizar o crear Blade y exportador. Si usa el transporte de OperacionesController, registrar tipo en selección de exportador, HTML y reconstrucción diferida.
5. Agregar rutas protegidas y servicio JS con descargas Blob.
6. Abrir VisorReportes desde HomeInfo con callbacks y contexto seleccionado; no añadir consultas específicas dentro del visor.
7. Comprobar vista/impresión, Excel/PDF, propietario/caducidad, correo, límites y coincidencia de importes.

## Cómo comprobar cambios sin tocar datos reales

En esta revisión se compararon tokens PHP ignorando comentarios, JavaScript/Vue normalizado, templates y Blade compilado con las copias previas; además se compiló app.scss. Esto verifica que la documentación no cambia comportamiento. Para cambios de cálculos, usar fixtures con operaciones/pagos pequeños en SQLite :memory: y contrastar resumen/detalle; no usar producción como prueba. El correo se sustituye por un emisor falso en las pruebas.

Los scripts de comprobación utilizados están en el workspace de trabajo, no se instalaron como suite del repositorio: work/verificar-documentacion-dashboard.php, work/verificar-documentacion-dashboard.cjs y work/check-dashboard-with-viewer.cjs. No ejecutar seeder, mantenimiento ni envíos reales para verificar comentarios.


## Expediente de ventas en cementerio: visor HTML (3 de noviembre de 2026)

`ReportesVentas.vue` usa exclusivamente `VisorReportes.vue` para sus PDF. Se retiró de este componente el Reporteador anterior y su estado de envío. Los botones de documentos, la apertura del acuse y los recibos posteriores a pagos usan el mismo `openReporte`.

### Catálogo y responsabilidades

`CementerioController::DOCUMENTOS_VISOR` permite únicamente: solicitud, convenio, titulo, estado-cuenta, referencias, reglamento, acuse-cancelacion, servicios-propiedad y recibo.

Se conservan las tres rutas parametrizadas de cementerio ya protegidas por auth:api; no se agregaron rutas. La consulta recibe venta_id; recibo exige además id_pago y comprueba con pagos_pagos_programados → pagos_programados → operaciones que corresponda a esa venta o sus cuotas (tipos 1/2). La generación del recibo permanece en PagosController::recibo_de_pago, invocado internamente; CementerioController coordina únicamente el contexto del expediente.

Los métodos heredados conservan sus datos, firmas, correos y salida PDF para los otros módulos que todavía los consumen. Con el atributo interno `_documento_cementerio_visor` devuelven HTML y opciones originales antes de output/inline. Un parámetro HTTP del mismo nombre no activa esta captura. `capturarDocumentoCementerioVisor` evita repetir la captura de Snappy.

El estado de cuenta conserva su consulta heredada de movimientos mediante el servicio backend; no se cambió el cálculo financiero. En pruebas esa comunicación se sustituye por respuestas ficticias.

### Vista, impresión y PDF

- La vista inicial es HTML con Open Sans, imágenes y CSS locales incrustados. No genera PDF ni descarga un Blob.
- Descarga/correo generan el PDF con las opciones capturadas y una instantánea privada del usuario/tipo de documento; al vencer responde 410, sin cerrar la sesión.
- El navegador toma tamaño y orientación del original: referencias en carta horizontal, recibo en A4, demás documentos en carta vertical. Conserva los márgenes individuales y el zoom de pantalla configurado por REPORTES_VISOR_ZOOM; imprimir vuelve a escala normal.
- Las cajas superiores de @page se dejan vacías para ocultar fecha y título automáticos en Chromium. El pie propio se conserva en las cajas inferiores; para otros navegadores que no soporten estas cajas puede ser necesario desactivar Encabezados y pies de página en su diálogo.
- Se respeta el aviso de cancelación/no validez propio de cada plantilla. No se presenta un acuse vigente como una operación cancelada.
- Firmas de reglamento, acuse y recibo agrupadas y compactas; primero se reduce el espacio vacío, sin deformar imágenes ni quitar nombres. Solicitud/Convenio conservan su tratamiento anterior. Se corrigió el logo del título para usar LOGOJPG. El pie del acuse se ajusta al margen corto para no cortar sus contactos.
- Chromium y wkhtmltopdf pueden distribuir las líneas de forma diferente: se verificaron contenido y legibilidad con datos ficticios, no una identidad de píxeles entre motores.

### Archivos y comprobaciones

Controladores: CementerioController.php y PagosController.php. Frontend: ReportesVentas.vue y reportesDocumentosCementerio.js. Plantillas ajustadas: titulo/titulo, reglamento_pago/reglamento, acuse_cancelacion/acuse y pagos/recibo_pago. Las otras plantillas se reutilizan sin modificar su contenido.

Se verificaron PHP, compilación de plantillas Vue2, los nueve llamados del frontend, acceso por usuario/tipo, recibos ajenos rechazados, PDF solo bajo demanda y correo simulado. Los siete documentos incorporados se renderizaron con sus Blade reales; se generaron PDF e impresión de navegador, revisando todas las páginas y comprobando que los encabezados automáticos estuvieran ausentes sin desactivarlos por la línea de comandos de Chrome. SQLite en memoria, datos y firmas ficticios; sin datos reales ni SMTP.

Para probar en el sistema: recompilar frontend con el comando habitual, recargar la aplicación y abrir cada documento. En un visor ya abierto, Actualizar renueva el HTML. Validar venta activa/cancelada, recibos de venta/cuotas y un expediente con muchos pagos. No se ejecutó Git.
