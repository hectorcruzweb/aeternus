<?php
namespace App\Exports;

/** Dibuja la silueta de la marca con rellenos continuos y alfa real.
 * Qt oscurece el PNG con alfa; trazar cada color de píxel producía costuras.
 * Se conservan forma, transparencia y los dos tonos medios (gris/dorado),
 * eliminando variaciones de compresión. No modifica el PNG original. */
class MarcaTituloReportes
{
    /** SVG cacheado por contenido: reemplazar el PNG renueva la marca automáticamente. */
    public static function svg(): string
    {
        $ruta = public_path('images/watermark.png');
        return \Illuminate\Support\Facades\Cache::remember('titulo_svg_liso_v4_'.hash_file('sha256', $ruta), now()->addDay(), function () use ($ruta) {
            $imagen = imagecreatefrompng($ruta); imagepalettetotruecolor($imagen);
            $ancho = imagesx($imagen); $alto = imagesy($imagen);
            $filas = []; $tonos = [0=>[0,0,0,0],1=>[0,0,0,0]];
            // Clasificar en los dos tonos de la marca; la intensidad del alfa
            // sigue proviniendo del archivo. No agregar blanco al área transparente.
            for ($y=0; $y<$alto; $y++) {
                $fila = [];
                for ($x=0; $x<$ancho; $x++) {
                    $p = imagecolorat($imagen,$x,$y); $op = 1-(($p>>24)&127)/127;
                    $r=($p>>16)&255; $g=($p>>8)&255; $b=$p&255;
                    $tono = $r > $b+5 ? 1 : 0;
                    $nivel = (int) round($op*32);
                    $fila[] = $nivel ? $tono.':'.$nivel : '';
                    if ($nivel) { $tonos[$tono][0]+=$r*$op; $tonos[$tono][1]+=$g*$op; $tonos[$tono][2]+=$b*$op; $tonos[$tono][3]+=$op; }
                }
                $filas[]=$fila;
            }
            imagedestroy($imagen); $rutas=[];
            foreach ($filas as $y=>$fila) {
                for ($x=0;$x<$ancho;) {
                    $clave=$fila[$x]; $fin=$x+1;
                    while ($fin<$ancho && $fila[$fin]===$clave) $fin++;
                    // Los tramos del mismo relleno forman una sola unión; un
                    // solape mínimo impide líneas blancas entre filas adyacentes.
                    if ($clave!=='') $rutas[$clave]=($rutas[$clave]??'').'M'.($x-0.05).' '.($y-0.05).'h'.($fin-$x+0.1).'v1.1H'.($x-0.05).'z';
                    $x=$fin;
                }
            }
            $svg='<svg xmlns="http://www.w3.org/2000/svg" width="'.$ancho.'" height="'.$alto.'" viewBox="0 0 '.$ancho.' '.$alto.'">';
            foreach ($rutas as $clave=>$d) {
                list($tono,$nivel)=explode(':',$clave); $t=$tonos[$tono]; $peso=max($t[3],0.00001);
                $rgb=round($t[0]/$peso).','.round($t[1]/$peso).','.round($t[2]/$peso);
                $svg.='<path fill="rgb('.$rgb.')" fill-opacity="'.number_format($nivel/32,5,'.','').'" d="'.$d.'"/>';
            }
            return $svg.'</svg>';
        });
    }
}
