# Catálogo de archivos del dashboard y sus reportes

Revisión: 31 de octubre de 2026. Rutas relativas a `C:/xampp/htdocs/aeternus`. Inventario de los 82 archivos documentados en esta integración; incluye dependencias compartidas relevantes, no una lista obtenida de Git. Las líneas son una referencia del estado revisado y pueden desplazarse.

Consultar también [Guía de mantenimiento](README.md), [Reglas](REGLAS.md) y [Visor y reportes](VISOR-Y-REPORTES.md).

## app/Http/Controllers/DashboardController.php

Construye el JSON del dashboard y decide la vista gerencial u operativa desde el usuario autenticado.

**Dónde ajustar:** Agregar bloques agregados en buildDashboard; no cargar aquí el desglose de los reportes.

**Se conecta con:** HomeInfo.vue; controladores de cada módulo; routes/api.php.

**Métodos localizables:** `__construct` (línea 23), `buildDashboard` (línea 38).

## app/Http/Controllers/OperacionesController.php

Motor compartido de ventas, capital pendiente, vencimientos y transporte de reportes del dashboard.

**Dónde ajustar:** Cálculos: getVentasSaldosOperacionesDashboard y getReporteVentasSaldosOperacionesDetallado. Transporte: preview*, datosReporteCementerio, htmlReporteCementerio y exportadorReporteVentas. Los nombres Cementerio también se usan para otros módulos.

**Se conecta con:** CementerioController; FunerariaController; SeguimientosController; app/Exports; VisorReportes.vue.

**Métodos localizables:** `previewResumenDashboard` (línea 29), `previewSeguimientosVencidosHistorico` (línea 83), `previewProgramadosProximosSieteDias` (línea 113), `previewSeguimientosRealizadosMes` (línea 143), `previewSolicitudesSinContrato` (línea 173), `getTiposOperacionesDashboard` (línea 206), `consultaPagosPorVencer` (línea 221), `getClientesPagosVencidosDashboard` (línea 252), `getReporteClientesPagosVencidos` (línea 259), `previewClientesPagosVencidos` (línea 264), `getPagosVenceHoyDashboard` (línea 270), `getPagosProximosSieteDiasDashboard` (línea 278), `resumenPagosPorVencer` (línea 286), `getReportePagosVenceHoy` (línea 304), `getReportePagosProximosSieteDias` (línea 309), `getReportePagosPorVencer` (línea 318), `previewPagosVenceHoy` (línea 407), `previewPagosProximosSieteDias` (línea 412), `previewPagosPorVencer` (línea 417), `anotarAlcanceVencido` (línea 448), `getGraficaVentasSaldosDashboard` (línea 465), `getVentasSaldosOperacionesDashboard` (línea 514), `getReporteVentasCementerio` (línea 677), `getReporteCuotasMantenimiento` (línea 683), `getReporteVentasSaldosOperacionesDetallado` (línea 697), `obtenerReporteIndividual` (línea 785), `getResumenGeneralOperaciones` (línea 806), `previewReporteGeneralOperaciones` (línea 836), `descargarReporteGeneralCompleto` (línea 873), `datosDetalleGeneralVentas` (línea 891), `exportadorReporteVentas` (línea 911), `previewVentasCementerio` (línea 927), `previewCuotasMantenimiento` (línea 971), `datosDetalleCuotas` (línea 1011), `usuarioReporteCementerio` (línea 1037), `datosReporteCementerio` (línea 1049), `htmlReporteCementerio` (línea 1108), `pdfReporteCementerio` (línea 1145), `descargarVentasCementerioExcel` (línea 1160), `descargarVentasCementerioPdf` (línea 1169), `enviarVentasCementerioCorreo` (línea 1182), `mantenerSaldosStatusOperaciones` (línea 1260).

## app/Http/Controllers/InventarioController.php

Resúmenes de inventario y consulta/exportación de artículos y servicios.

**Dónde ajustar:** getInventarioDashboard para indicadores; consultaReporteInventario para filtros; previewInventarioDashboard y datosReporteInventarioPreview para caché y límite de visualización. El valor de inventario actual usa precio_venta.

**Se conecta con:** InventarioExport.php; Inventario.vue; temporales_visor_excel/inventario/reporte.blade.php.

**Métodos localizables:** `getUltimoAjusteInventarioDashboard` (línea 45), `getInventarioDashboard` (línea 80), `previewInventarioDashboard` (línea 139), `construirPdfReporteInventario` (línea 225), `datosReporteInventarioPreview` (línea 305), `consultaReporteInventario` (línea 336), `get_reporte_existencias_costos` (línea 1959), `get_reporte_movimientos_inventario` (línea 2101), `get_reporte_inventario_con_rotacion` (línea 2510).

## app/Http/Controllers/CementerioController.php

Completa los reportes de operaciones 1 (ventas) y 2 (cuotas) con sus datos de cementerio.

**Dónde ajustar:** Reglas particulares en completarDetalleReporteCementerio; resumen/desglose por cuota en getResumenCuotasMantenimiento y getDetalleCuotaMantenimiento. Ubicaciones: getUbicacionesTextoReporte reutiliza ubicacion_texto.

**Se conecta con:** OperacionesController; CuotasMantenimientoExport; VentasCementerioExport.

**Métodos localizables:** `getReporteVentasCementerio` (línea 40), `getReporteCuotasMantenimiento` (línea 46), `getReporteCementerioPorTipo` (línea 51), `completarDetalleReporteCementerio` (línea 62), `getUbicacionesTextoReporte` (línea 104).

## app/Http/Controllers/FunerariaController.php

Provee servicios en proceso, solicitudes sin contrato y detalle de operaciones 3, 4 y 5.

**Dónde ajustar:** getServiciosFunerariosDashboard conserva ventana móvil de 72 horas cuando no hay eventos fechados; solicitudes recientes usan 7 días incluyendo hoy. Los métodos getReporte* completan los datos propios de cada operación.

**Se conecta con:** OperacionesController; ServiciosFunerarios.vue; reportesVentas.js.

**Métodos localizables:** `consultaSolicitudesSinContrato` (línea 53), `getSolicitudesSinContratoDashboard` (línea 72), `getReporteSolicitudesSinContrato` (línea 101), `previewReporteSolicitudesSinContrato` (línea 134), `previewReporteVentasGenerales` (línea 141), `getReporteVentasGenerales` (línea 153), `previewReporteVentasPlanes` (línea 172), `getReporteVentasPlanesFunerarios` (línea 185), `previewReporteServiciosFunerarios` (línea 212), `getReporteServiciosFunerarios` (línea 225), `getServiciosFunerariosDashboard` (línea 261).

## app/Http/Controllers/FacturacionController.php

Calcula timbres disponibles, CFDI con adeudo, facturado/cubierto del mes y vigencia de firma; construye sus reportes.

**Dónde ajustar:** getTimbresDashboard y resumenRecargasTimbres para lotes; consultaCfdisAdeudosBase para saldo fiscal; getFacturacionMesDashboard para importes del card; getVigenciaFirmaDashboard para facturacion.fecha_vencimiento.

**Se conecta con:** movimientos_timbres; cfdis_tipo_relacion; Facturacion.vue; exportadores Cfdis* y Timbres*.

**Métodos localizables:** `getVigenciaFirmaDashboard` (línea 2478), `getTimbresDashboard` (línea 2491), `resumenRecargasTimbres` (línea 2505), `consultaHistorialTimbres` (línea 2534), `autorizarReporteFacturacion` (línea 2551), `previewReporteTimbres` (línea 2559), `datosReporteTimbres` (línea 2608), `htmlReporteTimbres` (línea 2633), `descargarReporteTimbresExcel` (línea 2649), `construirPdfTimbres` (línea 2660), `descargarReporteTimbresPdf` (línea 2674), `enviarReporteTimbresCorreo` (línea 2683), `consultaCfdisAdeudosBase` (línea 2738), `resumenCfdisAdeudos` (línea 2762), `getCfdisAdeudosDashboard` (línea 2779), `detalleCfdisAdeudos` (línea 2788), `pagosRecibidosCfdisAdeudos` (línea 2804), `operacionesCfdisAdeudos` (línea 2824), `previewReporteCfdisAdeudos` (línea 2837), `datosCfdisAdeudos` (línea 2877), `htmlCfdisAdeudos` (línea 2898), `pdfCfdisAdeudos` (línea 2908), `descargarCfdisAdeudosExcel` (línea 2919), `descargarCfdisAdeudosPdf` (línea 2925), `enviarCfdisAdeudosCorreo` (línea 2932), `getFacturacionMesDashboard` (línea 2975), `previewReporteCfdisMes` (línea 2984), `datosCfdisMes` (línea 3025), `htmlCfdisMes` (línea 3046), `pdfCfdisMes` (línea 3056), `descargarCfdisMesExcel` (línea 3067), `descargarCfdisMesPdf` (línea 3073), `enviarCfdisMesCorreo` (línea 3080).

## app/Http/Controllers/SeguimientosController.php

Consulta actividad realizada, agenda de próximos 7 días e histórico vencido de seguimientos.

**Dónde ajustar:** Separar fecha efectiva del seguimiento y fecha programada. Las consultas del dashboard excluyen operaciones canceladas; consulta_historica permite revisar registros antiguos sin habilitar nuevas capturas sobre canceladas.

**Se conecta con:** SeguimientosResumen.vue; OperacionesController; InfoOperacion.vue; FormularioRegistrarSeguimiento.vue.

**Métodos localizables:** `consultaSeguimientosVencidosHistorico` (línea 71), `getSeguimientosVencidosHistoricoDashboard` (línea 79), `getReporteSeguimientosVencidosHistorico` (línea 86), `previewReporteSeguimientosVencidosHistorico` (línea 131), `consultaProgramadosProximosSieteDias` (línea 137), `getProgramadosProximosSieteDiasDashboard` (línea 144), `getReporteProgramadosProximosSieteDias` (línea 151), `previewReporteProgramadosProximosSieteDias` (línea 194), `consultaRealizadosMes` (línea 203), `getSeguimientosRealizadosMesDashboard` (línea 218), `getReporteSeguimientosRealizadosMes` (línea 225), `previewReporteSeguimientosRealizadosMes` (línea 275).

## app/Http/Controllers/ClientesController.php

Incluye la búsqueda de destinatarios de reportes entre clientes existentes.

**Dónde ajustar:** buscarCorreosReportes concentra la búsqueda por nombre/correo. No confundir sugerir un cliente con agregarlo automáticamente a la lista de destinatarios.

**Se conecta con:** EnviarReporteCorreo.vue; services/clientes.js; routes/api.php.

**Métodos localizables:** `buscarCorreosReportes` (línea 27).

## app/Http/Controllers/EmailController.php

Envía adjuntos del nuevo visor con el template corporativo y sanea el mensaje del editor.

**Dónde ajustar:** reporte_email cambia todos los destinos a hectorcrzprz@gmail.com si app.env es diferente de production; con varios receptores usa BCC. mensajeReporteSeguro define el HTML admitido. pdf_email conserva el flujo anterior.

**Se conecta con:** EnviarReporteCorreo.vue; resources/views/mails/mail.blade.php; configuración de correo.

**Métodos localizables:** `reporte_email` (línea 26), `mensajeReporteSeguro` (línea 60).

## routes/api.php

Declara las rutas autenticadas de indicadores, vistas previas, descargas y correo.

**Dónde ajustar:** Mantener los reportes dentro de auth:api. Los controladores validan además rol y propietario del token. El throttle de correo es independiente del acceso al visor.

**Se conecta con:** services/reportes*.js; DashboardController y controladores de reportes.

## resources/js/src/views/HomeInfo.vue

Coordina carga del dashboard, permisos recibidos, filtros de gráfica y apertura de todos los visores.

**Dónde ajustar:** _fetchDashboard carga el JSON; cambiarPeriodoGrafica/cargarGrafica actualizan solo la gráfica; programarActualizacionInactiva actualiza tras 5 minutos sin actividad. Las acciones superiores siguen el scroll y cambian de fondo al fijarse.

**Se conecta con:** services/dashboard.js; dashboard/*.vue; VisorReportes.vue; _dashboard.scss.

**Métodos localizables:** `programarActualizacionInactiva` (línea 287), `actualizarPorInactividad` (línea 294), `_fetchDashboard` (línea 460), `cargarGrafica` (línea 650).

## resources/js/src/views/dashboard/Facturacion.vue

Muestra timbres, adeudo fiscal histórico, facturado/cubierto del mes y vigencia de firma; emite el reporte seleccionado.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/Inventario.vue

Muestra artículos inventariables, servicios, faltantes, excedentes, último ajuste y valor inventariable; emite el filtro de reporte.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/ServiciosFunerarios.vue

Carrusel de servicios en proceso, datos operativos y acceso al expediente.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/VentasSaldos.vue

Carrusel de resúmenes recibido del backend, incluido todas; el enlace usa el tipo del slide activo.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/VentasCobrosGrafica.vue

Compara operaciones 1/3/4/5 en barras y muestra cuotas tipo 2 aparte; permite elegir mes/año y reporte.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend. Mantener responsive: [] de ApexCharts: la adaptación móvil usa matchMedia para evitar el error de recursión observado. Los filtros visuales no cambian el alcance del reporte elegido.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/PendientesResumen.vue

Indicadores y enlaces de pagos de hoy, próximos 7 días y solicitudes recientes sin contrato.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/SeguimientosResumen.vue

Indicadores y enlaces de realizados del mes, agenda semanal, vencidos históricos y clientes con pagos vencidos.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/VistaLimitada.vue

Presenta la vista operativa restringida con los servicios permitidos por el backend.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/DashboardSkeleton.vue

Plantillas de espera por tipo de card, antes de que existan datos y mientras se recargan.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/dashboard/MensajeTemporada.vue

Controla el mensaje de temporada con props explícitas; HomeInfo decide si está habilitado.

**Dónde ajustar:** Cambiar textos/composición aquí y estilos en _dashboard.scss; los importes y permisos proceden de HomeInfo y del backend.

**Se conecta con:** HomeInfo.vue; DashboardController; resources/sass/vuexy/_dashboard.scss.

## resources/js/src/views/pages/VisorReportes.vue

Modal reutilizable de vista previa HTML, impresión, descarga PDF/Excel y envío por correo.

**Dónde ajustar:** Recibe funciones mediante props, no consulta tablas. consultar valida el contrato hojas/token; el iframe muestra HTML sin generar XLSX. soloPdf oculta Excel; descargarCompleto habilita el Excel ampliado.

**Se conecta con:** EnviarReporteCorreo.vue; HomeInfo.vue; services/reportes*.js; templates Blade.

**Métodos localizables:** `consultar` (línea 385), `imprimir` (línea 471).

## resources/js/src/views/pages/EnviarReporteCorreo.vue

Editor de destinatarios, asunto, mensaje vue2-editor y formato del adjunto.

**Dónde ajustar:** Permite buscar clientes por nombre/correo, editar destinatarios, confirmar reemplazos y enviar hasta 20 receptores. El mensaje visible se limita a 5000 caracteres; el backend valida y sanea de nuevo.

**Se conecta con:** ClientesController@buscarCorreosReportes; EmailController@reporte_email; VisorReportes.vue.

## resources/js/src/views/pages/seguimientos/InfoOperacion.vue

Muestra la operación/cliente del seguimiento y permite consultar el vínculo histórico de una operación cancelada.

**Dónde ajustar:** Conservar seguimiento_historico_id al consultar; la excepción de lectura no autoriza registrar nuevos seguimientos sobre canceladas.

**Se conecta con:** FormularioRegistrarSeguimiento.vue; SeguimientosController.

## resources/js/src/views/pages/seguimientos/FormularioRegistrarSeguimiento.vue

Formulario de captura, atención y consulta de seguimientos.

**Dónde ajustar:** El modo consultar pasa consulta_historica y seguimiento_id; resultado_datos_cliente recibe el resultado del componente de información. No convertir la excepción histórica en permiso de edición.

**Se conecta con:** InfoOperacion.vue; SeguimientosController; services/seguimientos.js.

## resources/js/src/services/dashboard.js

Cliente HTTP del JSON principal y la gráfica por mes/año.

**Dónde ajustar:** Mantener _fetchDashboard separado de graficaVentasSaldos para no recargar las demás tarjetas al cambiar el periodo.

**Se conecta con:** HomeInfo.vue; routes/api.php.

## resources/js/src/services/reportesVentas.js

Ventas/saldos individuales y general; elige endpoint de funeraria para tipos 3/4/5 y operaciones para 1/2.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesTimbres.js

Recargas de timbres y su historial.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesCfdisAdeudos.js

CFDI con adeudo histórico.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesCfdisMes.js

Facturado y cubierto del mes.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesVenceHoy.js

Pagos programados pendientes que vencen hoy.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesProximosPagos.js

Pagos pendientes de hoy y los 6 días siguientes.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesSolicitudesSinContrato.js

Solicitudes funerarias sin contrato, recientes e históricas.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesSeguimientosRealizados.js

Seguimientos realizados del mes.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesSeguimientosProgramados.js

Agenda pendiente de próximos 7 días, incluyendo hoy.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesSeguimientosVencidos.js

Seguimientos pendientes vencidos históricos.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesClientesPagosVencidos.js

Clientes agrupados con sus pagos vencidos.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/reportesResumenDia.js

Resumen del día con datos del dashboard; solo PDF.

**Dónde ajustar:** visualizar obtiene metadatos/HTML; las descargas usan responseType blob y el token recibido. correo envía datos al backend, no usa SMTP desde el navegador.

**Se conecta con:** HomeInfo.vue; VisorReportes.vue; routes/api.php.

## resources/js/src/services/inventario.js

Cliente HTTP de inventario; incluye visualizarExcel y descargas/envío para el nuevo visor.

**Dónde ajustar:** Conservar contratos de funciones usadas por otros módulos; el nombre visualizarExcel es histórico y devuelve una vista HTML de reporte.

**Se conecta con:** InventarioController; HomeInfo.vue; VisorReportes.vue.

## resources/js/src/services/clientes.js

Cliente HTTP del módulo de clientes, incluida la búsqueda de correos/nombres para los reportes.

**Dónde ajustar:** La búsqueda devuelve sugerencias; la confirmación y la lista final se manejan en EnviarReporteCorreo.vue.

**Se conecta con:** ClientesController; EnviarReporteCorreo.vue.

## resources/js/src/utils/formato.js

Formateador monetario compartido por el dashboard.

**Dónde ajustar:** moneda convierte números a formato es-MX. El tamaño de MXN se controla en el marcado y SCSS de cada indicador, no en este formateador.

**Se conecta con:** dashboard/*.vue; _dashboard.scss.

## resources/js/src/utils/PopupManager.js

Administra la pila, foco y cierre de popups del proyecto.

**Dónde ajustar:** Revisar aquí superposición/foco entre visor, correo y confirmación; los estilos de título, X y espaciado viven en _dashboard.scss.

**Se conecta con:** plugins/popupManager.js; VisorReportes.vue; EnviarReporteCorreo.vue.

## resources/sass/vuexy/_dashboard.scss

Estilos del dashboard, skeletons, gráfica, visor, correo y ajustes compartidos de sus modales.

**Dónde ajustar:** Buscar por prefijos dashboard-, visor- y reporte-correo. La barra superior usa dashboard__acciones--superiores, sticky y fondo al fijarse; el margen superior compacto está al final. No contiene estilos del documento PDF/Excel.

**Se conecta con:** HomeInfo.vue; dashboard/*.vue; VisorReportes.vue; EnviarReporteCorreo.vue; Blade y Exports tienen estilos propios.

## app/Exports/InventarioExport.php

Construye y da formato al XLSX de inventario: logo, Arial, columnas, indicadores, protección y ajuste de impresión.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/VentasCementerioExport.php

Define secciones/columnas del Excel individual de ventas y saldos; se reutiliza para los distintos tipos de operación.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/VentasCementerioHoja.php

Motor visual de hojas Excel compartido: encabezado, logo, formatos numéricos, alineación y estilos por sección.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/ResumenGeneralOperacionesExport.php

Excel general de las operaciones; agrega las hojas de detalle cuando se solicita completo.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/CuotasMantenimientoExport.php

Excel de cuotas: resumen y organización del detalle por cuota según el modo solicitado.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/CuotaMantenimientoHoja.php

Hoja Excel particular para presentar el detalle de una cuota de mantenimiento.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/TimbresRecargasExport.php

Organiza el Excel de lotes de timbres; el completo agrega el historial de cada lote en hojas separadas.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/TimbresRecargasHoja.php

Presentación y formato de cada hoja Excel de recargas y consumos de timbres.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/CfdisAdeudosExport.php

Organiza el reporte Excel de CFDI con adeudo, operaciones relacionadas y pagos recibidos.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/CfdisAdeudosHoja.php

Da formato a las hojas de facturación y presenta agrupados los datos complementarios de las facturas.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/CfdisMesExport.php

Organiza el Excel de facturado/cubierto del mes, usando el diseño compartido de facturación.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/PagosVenceHoyExport.php

Define las hojas/columnas de pagos próximos y de hoy; recibe el alcance ya calculado por el controlador.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/SolicitudesSinContratoExport.php

Define resumen y detalle de solicitudes funerarias sin contrato.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/SeguimientosRealizadosExport.php

Define resumen y detalle de gestiones realizadas del mes.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/SeguimientosProgramadosExport.php

Define resumen y detalle de la agenda de seguimientos próximos.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/SeguimientosVencidosExport.php

Define resumen y detalle de seguimientos pendientes históricos.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/ClientesPagosVencidosExport.php

Organiza resumen por tipo, clientes y desglose de sus pagos vencidos.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/ClientesPagosVencidosDetalleHoja.php

Hoja agrupada que muestra una sola cabecera por cliente y todos sus pagos/operaciones debajo.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/ResumenDashboardExport.php

Contiene el organizador de secciones del resumen diario. La ruta actual limita este reporte a PDF; no habilitar Excel solo por existir esta clase.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/NombreEmpresaReportes.php

Normaliza el nombre comercial para reportes respetando nombres ya definidos en mayúsculas.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## app/Exports/DireccionReportes.php

Compone y normaliza la dirección de empresa para reportes y correo sin cambiar los datos de origen.

**Dónde ajustar:** Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.

**Se conecta con:** app/Http/Controllers; resources/views/reportes/temporales_visor_excel.

## resources/views/reportes/temporales_visor_excel/inventario/reporte.blade.php

Vista HTML/PDF de inventario; recibe columnas/filas del exportador para mantener el mismo contenido.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/ventas_cementerio/reporte.blade.php

Vista compartida de reportes individuales de ventas y saldos de los cinco tipos; muestra datos específicos según el tipo.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/general_operaciones/resumen.blade.php

Resumen general con comparación de tipos 1/3/4/5 y cuotas tipo 2 separadas para evitar distorsionar la escala.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/cuotas_mantenimiento/resumen.blade.php

Resumen por cuota y enlaces autorizados para abrir sus desgloses desde el visor.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/facturacion/timbres.blade.php

Vista de recargas/disponibles y consumos asociados a cada lote cuando se incluye detalle.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/facturacion/adeudos.blade.php

Vista compartida de adeudos históricos y facturado del mes; factura principal y, debajo, operaciones y pagos recibidos.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/pagos/vence_hoy.blade.php

Vista compartida de pagos de hoy y próximos 7 días; el título/alcance se reciben del backend.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/pagos/clientes_vencidos.blade.php

Desglose de pagos vencidos agrupados por cliente, con operación, referencia y contacto.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/funeraria/solicitudes_sin_contrato.blade.php

Solicitudes recientes para seguimiento y solicitudes antiguas por revisar; antigüedad en lenguaje humano.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/seguimientos/realizados_mes.blade.php

Gestiones realizadas durante el mes: cliente, motivo, medio, resultado y responsable.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/seguimientos/programados_proximos.blade.php

Agenda pendiente de los próximos 7 días, con contactos y programación.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/seguimientos/vencidos_historico.blade.php

Pendientes históricos de atención y atraso en lenguaje humano.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/dashboard/resumen.blade.php

Resumen diario imprimible en carta vertical, solo PDF; servicios en pares, secciones compactas y tabla de ventas para impresión.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/reportes/temporales_visor_excel/compartidos/footer_pdf.blade.php

Pie PDF común: paginación abreviada, dominio y fecha de generación.

**Dónde ajustar:** Estilos y distribución de vista previa/impresión aquí. Los bloques modoPdf y @media print pueden diferir de pantalla; el tamaño/márgenes del PDF se configuran también en el controlador.

**Se conecta con:** VisorReportes.vue; controlador del reporte; exportador Excel equivalente.

## resources/views/mails/mail.blade.php

Template corporativo del correo: logo, saludo, mensaje, adjunto y contacto de empresa.

**Dónde ajustar:** Modificar aquí estética del cuerpo, dirección y leyendas de no responder/confidencialidad. También lo usa el envío antiguo pdf_email: comprobar ambas variantes.

**Se conecta con:** EmailController; NombreEmpresaReportes; DireccionReportes.

## database/migrations/2026_10_29_000001_create_movimientos_timbres_table.php

Crea el libro de movimientos de timbres con recargas, consumos y ajustes.

**Dónde ajustar:** cfdis_id enlaza el consumo a la factura; recarga_id apunta al lote de esta misma tabla. referencia es un identificador/UUID, no una FK. No editar migraciones ya aplicadas para cambiar producción: usar una nueva.

**Se conecta con:** HistorialTimbresSeeder; FacturacionController.

## database/seeds/HistorialTimbresSeeder.php

Reconstruye consumos históricos por fechas y disponibilidad FIFO de tres recargas confirmadas.

**Dónde ajustar:** RECARGAS configura 2000/2000/500. Incluye CFDI cancelados que fueron timbrados; asignacion_estimada distingue la reconstrucción del historial real del proveedor. No registra automáticamente consumos futuros.

**Se conecta con:** movimientos_timbres; FacturacionController; timbrado futuro pendiente de integración específica.

## app/Console/Commands/ActualizarSaldosOperaciones.php

Entrada Artisan de mantenimiento de saldo/status, separada de la lectura del dashboard.

**Dónde ajustar:** OPERACIONES_STATUS_0 contiene operaciones.id verificadas (3412 y 897), no IDs de servicios. Sin --aplicar simula; --tipo limita los tipos. La actualización real se delega a mantenerSaldosStatusOperaciones.

**Se conecta con:** OperacionesController; php artisan help operaciones:actualizar-saldos.
