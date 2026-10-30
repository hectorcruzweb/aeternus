<?php
namespace App\Exports;

/** Presentación de la dirección corporativa; no cambia los campos almacenados. */
class DireccionReportes
{
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
    public static function formatear($empresa): string
    {
        $campo=function($nombre)use($empresa){return is_array($empresa)?($empresa[$nombre] ?? ''):($empresa->$nombre ?? '');};
        // El número exterior se conserva literalmente, incluidos S/N, guiones y letras.
        $calle=trim(self::texto($campo('calle')).' '.trim((string)$campo('num_ext')));
        $localidad=implode(', ',array_filter([self::texto($campo('ciudad')),self::texto($campo('estado'))]));
        return implode(' · ',array_filter([$calle,self::texto($campo('colonia')),$localidad]));
    }
}
