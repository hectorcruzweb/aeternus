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
