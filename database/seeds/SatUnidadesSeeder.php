<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatUnidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_unidades')->insert(['clave' => 'H87', 'unidad' => 'Pieza', 'descripcion' => 'Unidad de conteo que define el número de piezas (pieza: un solo artículo, artículo o ejemplar).']);
        DB::table('sat_unidades')->insert(['clave' => 'E48', 'unidad' => 'Unidad de servicio', 'descripcion' => 'Unidad de conteo que define el número de unidades de servicio (unidad de servicio: definido período / propiedad / centro / utilidad de alimentación).']);
        DB::table('sat_unidades')->insert(['clave' => 'XPK', 'unidad' => 'Paquete', 'descripcion' => 'Unidad de empaque estándar.']);
        DB::table('sat_unidades')->insert(['clave' => 'DAY', 'unidad' => 'Día', 'descripcion' => 'Se denomina día (del latín dies) al lapso que tarda la Tierra desde que el Sol está en el punto más alto sobre el horizonte hasta que vuelve a estarlo.']);
        DB::table('sat_unidades')->insert(['clave' => 'HUR', 'unidad' => 'Hora', 'descripcion' => 'Es una unidad de tiempo que se corresponde con la vigésimo-cuarta parte de un día solar medio.']);
        DB::table('sat_unidades')->insert(['clave' => 'DZN', 'unidad' => 'Docena', 'descripcion' => 'Unidad de recuento de definir el número de unidades en múltiplos de 12.']);
        DB::table('sat_unidades')->insert(['clave' => 'KGM', 'unidad' => 'Kilogramo', 'descripcion' => 'Una unidad de masa igual a mil gramos.']);
        DB::table('sat_unidades')->insert(['clave' => 'MTK', 'unidad' => 'Metro cuadrado', 'descripcion' => 'Es la unidad básica de superficie en el Sistema Internacional de Unidades. Si a esta unidad se antepone un prefijo del Sistema Internacional se crea un múltiplo o submúltiplo de esta.']);
        DB::table('sat_unidades')->insert(['clave' => 'MTR', 'unidad' => 'Metro', 'descripcion' => 'El metro (símbolo m) es la principal unidad de longitud del Sistema Internacional de Unidades. Un metro es la distancia que recorre la luz en el vacío en un intervalo de 1/299 792 458 de segundo.']);
    }
}