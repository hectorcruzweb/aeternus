{{-- Reglas compartidas para PDF e impresión; no agrupar tablas completas. --}}
<style>
/* Paginación común: evita títulos y líneas huérfanas sin impedir que las
   tablas o párrafos extensos continúen en otra hoja. No reduce la escala. */
@media print {
    p { orphans:3; widows:3; }
    h1, h2, h3, h4 { page-break-after:avoid; break-after:avoid-page; }
    .nota, .nota-reporte { page-break-before:avoid; break-before:avoid-page; }
    .reporte-bloque-breve { page-break-inside:avoid; break-inside:avoid-page; }
}
</style>
