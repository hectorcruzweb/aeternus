# Listado de pagos con filtros independientes

## Archivos y entrada

- `resources/js/src/views/pages/pagos/ListadoPagosFiltros.vue`: nuevo listado; conserva tabla y acciones de captura, cancelación y recibos en VisorReportes. Utiliza cliente_texto para admitir varias aplicaciones/clientes y datos faltantes.
- `FiltrosPagos.vue` en el mismo directorio: presentación, fechas españolas, select combinado pago/referencia, chips y limpieza. Emite change; no consulta la API.
- `resources/js/src/services/filtrarPagos.js`: solicitud con cancelación propia.
- `PagosController::FiltrarPagos` valida y pagina; consultaFiltrarPagos construye su consulta exclusiva.
- Ruta autenticada `GET pagos/filtrar`. Router `/cobranza/pagos` carga el componente nuevo.

PagosLista.vue y get_pagos no se modificaron. Para volver al listado anterior, cambiar solo el import del router a PagosLista.vue.

## Reglas de búsqueda

Número de pago exacto = pagos.id. Referencia exacta = pagos_programados.referencia_pago, conservando los ceros iniciales; no es el número bancario/de depósito de pagos.referencia. Cliente por palabras combinadas, sin importar el orden; % y _ se tratan literalmente.

Número de operación es la clave visible, nunca operaciones.id: tipo 1 ventas_terrenos_id; tipo 2 ventas_terrenos_id (venta de la propiedad de la cuota); tipo 3 servicios_funerarios_id; tipo 4 ventas_planes_id; tipo 5 ventas_generales_id. Sin tipo elegido se evalúan estas claves por tipo. El mapa TIPOS_FILTRO_PAGOS centraliza las etiquetas y campos.

Los cinco tipos de movimiento se definen en MOVIMIENTOS_FILTRO_PAGOS, según los códigos confirmados de pagos.movimientos_pagos_id; no depende de la tabla auxiliar movimientos_pagos. Estado del pago: Todos, Activos (1), Cancelados (0). Rango sobre pagos.fecha_pago, con ambos días incluidos; hasta se convierte al día siguiente exclusivo para admitir campos con hora.

Los predicados cliente/tipo/número/referencia comparten un EXISTS sobre la misma aplicación para evitar coincidencias cruzadas. Un movimiento aparece una sola vez aunque tenga varias aplicaciones. No se excluye un pago por cancelación posterior de su operación: es historial. Los movimientos principales y asociados siguen disponibles. No se modifica ni recalcula saldo. La tabla conserva monto_pago como importe del movimiento; no se reemplaza por total_pago (cantidad recibida).

## Rendimiento y vista

Paginación SQL fija de 15, orden fecha_pago descendente e id descendente. Solo se consultan los detalles de la página mediante una segunda consulta agrupada; no hay consultas por cada fila. Catálogos se solicitan una sola vez por apertura. Debounce de 300 ms, token de cancelación local y contador de versión evitan que respuestas antiguas reemplacen filtros nuevos. Al cambiar filtros se reinicia a página 1.

Fechas ISO en API y texto humano español en pantalla, X independiente para limpiarlas. Los nuevos estilos son locales. No requiere migración ni cambios de .env. Recompilar frontend para publicar y conservar los pasos habituales de despliegue del proyecto.

## Verificación realizada

Consulta verificada con SQLite en memoria: paginación, aplicaciones múltiples, coincidencia conjunta de filtros, números visibles, referencias con ceros, clientes por palabras, cancelados, movimientos asociados y límites de fechas. Compilación de templates Vue y sintaxis Babel/PHP. Card comprobada aisladamente con Chrome a 1920, 1440, 1366, 1024, 768 y 390 px. No se capturaron ni cancelaron pagos reales durante la verificación.

## Reporte PDF / Excel del listado

El enlace Reporte del listado abre el visor con una copia de los filtros seleccionados. No incluye page ni limita a los 15 registros visibles. FiltrarPagos y reporteListadoPagos comparten validarFiltrosPagos, consultaFiltrarPagos y completarFilasPagos. Una fila por movimiento, con todas sus referencias y números visibles de venta/servicio; nunca se muestra operaciones.id.

Rutas autenticadas: GET pagos/reporte-filtrado y GET pagos/reporte-filtrado/{token}/{formato}, donde formato admite pdf o excel. La instantánea tiene propietario y caduca a los 15 minutos con respuesta 410, sin invalidar la sesión. Hasta 5000 filas se visualiza HTML. Por encima se ofrece descarga completa; al generarla se conserva esa captura para los siguientes formatos. Ninguna operación escribe pagos/saldos.

El resumen muestra conteos y separa el importe de movimientos activos por tipo. Cancelados quedan fuera de esos importes y siguen visibles en el detalle si coinciden con el filtro. No sumar total_pago: puede repetirse en submovimientos del recibo. No se presenta la suma de descuentos como dinero recibido.

ListadoPagosExport utiliza VentasCementerioHoja con Arial, logo y protección; contiene las hojas Pagos y Resumen. Los importes son numéricos y las referencias son texto literal. La plantilla se encuentra en resources/views/reportes/temporales_visor_excel/pagos/listado.blade.php. El PDF usa carta vertical, encabezados repetidos y footer compartido con fecha/dominio/página. Reutiliza REPORTES_VISOR_ZOOM y REPORTES_MARCA_AGUA_PAGOS; no agrega variables al .env.

Verificado con datos de prueba: alcance completo, igualdad de filtros, aplicaciones múltiples sin duplicados, separación de descuentos, HTML y Excel generados, tokens de otros usuarios rechazados y vencimiento 410. PDF generado con wkhtmltopdf y revisado visualmente en tamaño carta.


### Listados extensos: solo Excel

`REPORTES_PAGOS_LIMITE_PDF=1000` establece el máximo de movimientos para PDF. Se lee en `config/reportes.php`; después de cambiarlo en un servidor con configuración cacheada, ejecutar `php artisan config:cache`.

Al superar el límite, la consulta no renderiza HTML ni genera PDF: entrega `pdfDisponible: false`, el visor explica el motivo y permite descargar Excel completo. Impresión y PDF quedan deshabilitados. La descarga valida nuevamente el límite en el servidor. Los demás reportes conservan su comportamiento si no envían esta capacidad.

Un PDF dentro del límite dispone de 180 segundos en su instancia de Snappy y tablas de hasta 100 movimientos para reducir el trabajo de paginación. Si aun así vence el tiempo, esa consulta deshabilita PDF y conserva Excel; el visor muestra el mensaje recibido. El número de filas es una medida preventiva, no una garantía del tiempo de renderizado. No se cambia la configuración global de Snappy.
