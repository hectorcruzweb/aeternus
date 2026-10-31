<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Normaliza el nombre comercial para reportes respetando nombres ya definidos en mayúsculas.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;

/** Presentación de marca: conserva mayúsculas deliberadas, sin modificar la BD. */
class NombreEmpresaReportes
{
    /**
     * Detalle del método: Normaliza Aeternus Funerales respetando una marca ya escrita completamente en
     * mayúsculas.
     * Entradas: $valor (valor que se debe formatear).
     * Salida / efectos: Texto para los encabezados/correos; no modifica los datos originales.
     */
    public static function formatear($valor): string
    {
        $nombre = trim((string)$valor);
        if ($nombre === '') return 'Aeternus Funerales';
        return preg_replace_callback('/\baeternus\s+funerales\b/iu', function ($m) {
            return $m[0] === mb_strtoupper($m[0], 'UTF-8') ? 'AETERNUS FUNERALES' : 'Aeternus Funerales';
        }, $nombre);
    }
}
