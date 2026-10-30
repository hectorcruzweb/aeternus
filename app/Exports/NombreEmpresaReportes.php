<?php
namespace App\Exports;

/** Presentación de marca: conserva mayúsculas deliberadas, sin modificar la BD. */
class NombreEmpresaReportes
{
    public static function formatear($valor): string
    {
        $nombre = trim((string)$valor);
        if ($nombre === '') return 'Aeternus Funerales';
        return preg_replace_callback('/\baeternus\s+funerales\b/iu', function ($m) {
            return $m[0] === mb_strtoupper($m[0], 'UTF-8') ? 'AETERNUS FUNERALES' : 'Aeternus Funerales';
        }, $nombre);
    }
}
