{{-- Zoom solo de visualización. Config central validada en config/reportes.php:
     REPORTES_VISOR_ZOOM=135 equivale a 35% más grande. No cambia tablas ni ancho
     interno de la hoja. PDF se excluye explícitamente y la impresión vuelve a 100%. --}}
<style id="zoom-visor-listados">
@media screen {
    body:not(.modo-pdf) .reporte { zoom: {{ number_format(config('reportes.visor_zoom_porcentaje', 135) / 100, 2, '.', '') }}; }
}
@media print {
    .reporte { zoom: 1 !important; }
}
</style>
