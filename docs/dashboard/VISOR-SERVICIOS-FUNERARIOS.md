# Visor del expediente de servicios funerarios (tipo 3)

## Alcance actual

**Solicitud de Servicio** y **Autorización de Servicio Funerario** de
`ReportesServicio.vue` utilizan el visor nuevo.
Su emisor es `FunerariaController::get_hoja_solicitud`, no `documento_solicitud`
(este último pertenece a ventas de planes funerarios).
Los demás documentos y recibos continúan en Reporteador hasta su migración.

## Recorrido

1. `openReporte` identifica `/funeraria/get_hoja_solicitud`, conserva `servicio_id`
   y abre `VisorReportes` con los callbacks del servicio JS.
2. `previewDocumentoServicio` valida autenticación, catálogo cerrado e `id_servicio`.
   No exige contrato: una solicitud puede existir sin operación asociada.
3. Clona el Request y asigna el atributo interno `_documento_servicio_visor`.
   Los parámetros HTTP externos no habilitan la captura.
4. `get_hoja_solicitud` reutiliza datos, empresa y firmas de áreas 15/16. Captura
   HTML y opciones Snappy antes de ejecutar `inline` o enviar correo.
5. `App\Services\VisorDocumentos` incrusta CSS, Open Sans e imágenes públicas
   locales, guarda una instantánea privada y adapta el HTML para visor/impresión.
6. Descargar o enviar genera el PDF desde esa instantánea y sus opciones originales.

## Rutas agrupadas

Dentro de `auth:api`, prefijo `funeraria/servicios/reportes`:

- `GET {documento}`: HTML, recibe `id_servicio`.
- `GET {documento}/{token}/pdf`: PDF bajo demanda.
- `POST {documento}/{token}/correo`: nuevo correo, solo formato PDF; límite de envío existente.

Las claves permitidas son `solicitud` y `autorizacion`. Para agregar otro documento,
ampliar `DOCUMENTOS_SERVICIO_VISOR`, la captura interna de su emisor, el catálogo
JS y la selección del componente. No agregar rutas por cada documento.

## Archivos y responsabilidades

- `app/Http/Controllers/FunerariaController.php`: catálogo, validación de servicio,
  coordinación y captura del emisor original.
- `app/Services/VisorDocumentos.php`: motor compartido de preparación visual,
  recursos, instantáneas, descarga y correo. Cementerio también delega aquí la
  preparación HTML y captura, conservando sus validaciones y rutas anteriores.
- `resources/js/src/services/reportesDocumentosServicio.js`: tres llamadas HTTP.
- `resources/js/src/views/pages/funeraria/servicios_funerarios/ReportesServicio.vue`:
  apertura y callbacks; mantiene el firmador y los otros documentos existentes.
- `resources/views/funeraria/hoja_solicitud_servicio_funerario/hoja_solicitud.blade.php`:
  documento original; firmas agrupadas y tamaño limitado para reducir saltos.

## Formato y seguridad

- Carta vertical, márgenes originales de 12.4 mm, pie y aviso de cancelado originales.
- Open Sans incrustada; zoom de pantalla usa `REPORTES_VISOR_ZOOM` existente.
- Ajuste `css_navegador` interno compensa la escala de 15px de Qt sin cambiar el PDF.
- Sin Blob/PDF inicial. La impresión usa el HTML; descarga/correo usan Snappy.
- Token de 64 caracteres hexadecimales, 15 minutos, ligado a usuario/documento.
- Prefijo `funeraria_servicios_` separado de `cementerio_solicitud_`.
- Caducidad 410, usuario ajeno 403: no se cierra la sesión por caducar un reporte.
- Correo comparte las validaciones del visor y `EmailController::reporte_email`,
  incluido el destino de desarrollo ya configurado fuera de production.
- No se permite cargar imágenes externas ni archivos fuera de `public`.
- El área posterior al contenido lleva la trama discreta ESPACIO SIN CONTENIDO,
  oculta en el visor y visible en PDF/impresión. Se controla independientemente:

```dotenv
REPORTES_MARCA_AGUA_SERVICIOS_FUNERARIOS=true
```

`false` la desactiva. La clave se añadió a `.env.example`; el `.env` real no se
modificó. Si la configuración está cacheada, ejecutar `php artisan config:clear`
y actualizar las instantáneas abiertas. Esta marca visual identifica el área libre;
no bloquea técnicamente la edición del PDF.

## Validación realizada

Datos ficticios y SQLite en memoria: catálogo, existencia, autenticación,
propietario, separación de módulos, cancelados, descarga, correo simulado y 410.
Render real de la plantilla con firmas sintéticas: una página carta en Snappy y
en Chrome, revisada visualmente. Regresión de los documentos de cementerio correcta.
No se consultó la base real ni se enviaron correos reales.

Pendiente del programador: recompilar frontend y revisar una solicitud real con
observaciones extensas y firmas actuales. Los motores pueden distribuir líneas
de forma diferente en documentos con contenido variable.

## Autorización de Servicio Funerario

Emisor: `hoja_preautorizacion`. Plantilla: `funeraria/hoja_preautorizacion/documento`.
Usa las firmas originales de otorgante, aceptante y testigos (áreas 17, 18, 19, 20).
Conserva contratante/cliente y selecciones originales de preparación, inhumación,
cremación y traslado. No cambia los textos legales ni el cálculo de datos.

Carta vertical con márgenes laterales 16.4mm, superior 12.4mm e inferior 24.4mm.
Texto del cuerpo 17px en Qt y equivalente 13.6px en navegador, interlineado 1.65,
menor separación vertical y firmas de hasta 150x48px, agrupadas para evitar cortes.
No se ocultan datos para forzar una página: contenido extraordinariamente largo
puede continuar. La prueba ficticia cabe con las cuatro firmas en una hoja tanto
en PDF como al imprimir con Chrome. Comparte la marca de espacio libre y su env.

La apertura selecciona `documentoServicioVisor` a partir de un mapa cerrado de
enlaces; los callbacks del mismo visor usan esa clave para consultar/descargar/enviar.
No se agregaron rutas nuevas al incorporar esta autorización.

## Solicitud compacta

La solicitud también compacta su tabla a 14px en Qt (11.2px equivalentes en
navegador), padding vertical de 6px, margen superior de 14px y firmas de hasta
150x48px. El espacio de observaciones vacías se limita a 24px, sustituyendo la utilidad pb-60.
Conserva toda la información y ambas firmas juntas. Contenido extraordinariamente
largo puede continuar, sin ocultar datos ni reducirlos indefinidamente.
