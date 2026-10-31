<?php
/**
 * DOCUMENTACIÓN DE MANTENIMIENTO — DASHBOARD
 * Compone y normaliza la dirección de empresa para reportes y correo sin cambiar los datos de origen.
 * Dónde ajustar: Los datos se calculan en los controladores. Cambiar aquí columnas, nombres de hojas y
 * presentación de XLSX; sincronizar títulos con la plantilla Blade equivalente.
 * Relacionado: app/Http/Controllers; resources/views/reportes/temporales_visor_excel
 * Guía del proyecto: docs/dashboard/README.md; catálogo: docs/dashboard/ARCHIVOS.md.
 */

namespace App\Exports;

/** Presentación de la dirección corporativa; no cambia los campos almacenados. */
class DireccionReportes
{
    /**
     * Detalle del método: Normaliza un componente textual de la dirección conservando una presentación
     * legible.
     * Entradas: $valor (valor que se debe formatear).
     * Salida / efectos: Texto normalizado; no actualiza la dirección guardada en la base de datos.
     */
    public static function texto($valor): string
    {
        $texto=preg_replace('/\s+/u',' ',trim((string)$valor));
        if($texto==='')return '';
        $texto=mb_convert_case($texto,MB_CASE_TITLE,'UTF-8');
        // Correcciones de nombres confirmados de la dirección de la empresa.
        $texto=preg_replace('/\bAdolfo Lopez Mateos\b/u','Adolfo López Mateos',$texto);
        $texto=preg_replace('/\bMazatlan\b/u','Mazatlán',$texto);
        // Artículos y preposiciones interiores se conservan en minúscula.
        $texto=preg_replace_callback('/(?<=\s)(De|Del|La|Las|Los|El|Y|En|Al)(?=\s|$)/u',function($m){return mb_strtolower($m[0],'UTF-8');},$texto);
        return $texto;
    }
    /**
     * Detalle del método: Compone la dirección corporativa normalizando sus componentes.
     * Entradas: $empresa (datos de empresa usados por el encabezado).
     * Salida / efectos: Texto para los encabezados/correos; no modifica los datos originales.
     */
    public static function formatear($empresa): string
    {
        $campo=function($nombre)use($empresa){return is_array($empresa)?($empresa[$nombre] ?? ''):($empresa->$nombre ?? '');};
        // El número exterior se conserva literalmente, incluidos S/N, guiones y letras.
        $calle=trim(self::texto($campo('calle')).' '.trim((string)$campo('num_ext')));
        $localidad=implode(', ',array_filter([self::texto($campo('ciudad')),self::texto($campo('estado'))]));
        return implode(' · ',array_filter([$calle,self::texto($campo('colonia')),$localidad]));
    }
}
