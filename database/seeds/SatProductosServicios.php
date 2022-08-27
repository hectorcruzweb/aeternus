<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatProductosServicios extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_productos_servicios')->insert(['clave' => '42262102', 'descripcion' => 'Paquetes mortuorios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '85171500', 'descripcion' => 'Servicios funerarios y asociados', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '48131502', 'descripcion' => 'Ataúdes', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '48131501', 'descripcion' => 'Cajas de cenizas funerarias o urnas de cremación', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '48131500', 'descripcion' => 'Productos de entierro o tumbas', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262100', 'descripcion' => 'Equipo y suministros funerarios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '90101700', 'descripcion' => 'Servicios de cafetería', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '48131504', 'descripcion' => 'Lápidas', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '48131503', 'descripcion' => 'Mortajas', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '60106504', 'descripcion' => 'Servicio de mesa para ritos fúnebres', 'mostrar_b' => 1]);

        DB::table('sat_productos_servicios')->insert(['clave' => '25101934', 'descripcion' => 'Coche fúnebre', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '76122200', 'descripcion' => 'Servicios de incineración de desechos', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '47121703', 'descripcion' => 'Urnas de incineración o accesorios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '47121705', 'descripcion' => 'Bolsas de arena para urnas de incineración', 'mostrar_b' => 1]);

        DB::table('sat_productos_servicios')->insert(['clave' => '42261810', 'descripcion' => 'Contenedores para transporte de cuerpos', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '70111705', 'descripcion' => 'Servicios de mantenimiento de cementerios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '10326000', 'descripcion' => 'Especies individuales o variedades de flores cortadas frescas', 'mostrar_b' => 1]);

        DB::table('sat_productos_servicios')->insert(['clave' => '42262007', 'descripcion' => 'Agujas inyectoras para embalsamar', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261706', 'descripcion' => 'Estaciones de trabajo para embalsamar o accesorios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261707', 'descripcion' => 'Estaciones de trabajo para autopsias de drenaje o accesorios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261800', 'descripcion' => 'Equipo y suministros de transporte y almacenaje del cadáver', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261801', 'descripcion' => 'Gabinetes de almacenamiento de cadáveres', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261802', 'descripcion' => 'Transportadores de cadáveres', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261803', 'descripcion' => 'Poleas de elevación de tijeras para cadáveres', 'mostrar_b' => 1]);

        DB::table('sat_productos_servicios')->insert(['clave' => '42261807', 'descripcion' => 'Carritos de autopsia', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261704', 'descripcion' => 'Tablas para necropsia o accesorios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261703', 'descripcion' => 'Tablas o accesorios para autopsias', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261809', 'descripcion' => 'Dispositivos de elevación o transferencia de cadáveres', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42261900', 'descripcion' => 'Equipo y suministros forenses clínicos', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262000', 'descripcion' => 'Equipo y suministros de embalsamar', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262001', 'descripcion' => 'Inyectores de cavidad para embalsamar', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262002', 'descripcion' => 'Tubos de drenaje de venas para embalsamar', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262003', 'descripcion' => 'Fluidos para embalsamar o tratamientos químicos', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262004', 'descripcion' => 'Tubos de inyección para embalsamar', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262005', 'descripcion' => 'Lavaderos o accesorios para embalsamar', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262006', 'descripcion' => 'Kits para embalsamar', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262103', 'descripcion' => 'Lienzos mortuorios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262104', 'descripcion' => 'Aspiradoras mortuorias', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262105', 'descripcion' => 'Compuestos endurecedores mortuorios', 'mostrar_b' => 1]);
        DB::table('sat_productos_servicios')->insert(['clave' => '42262101', 'descripcion' => 'Trajes mortuorios', 'mostrar_b' => 1]);

        DB::table('sat_productos_servicios')->insert(['clave' => '84111506', 'descripcion' => 'Servicios de facturación', 'mostrar_b' => 0]);
    }
}