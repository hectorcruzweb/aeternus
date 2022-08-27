<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMovimientos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * TIPOS DE MOVIMIENTO
         * Entrada
         * 1- (AI) Ajustes de inventario
         * 2- (EA) Entrada de lote por ajustes
         * 3- (CM) Compra de mercancías a proveedor
         * 4- (CV) Cancelación de ventas
         * Salidas
         * 5- (CC) Cancelación de compra de mercancías a proveedor
         * 6- (DP) Devolución a proveedores
         * 7- (SC) Salida de mercancía caducada
         * 8- (SM) Salida de mercancía especial
         * 9- (VM) Venta de mercancías
         */
        DB::table('tipo_movimientos')->insert(['tipo' => 'Ajustes de Inventario', 'clave' => 'AI']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Entrada de lote por ajustes', 'clave' => 'EA']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Compra de mercancías a proveedor', 'clave' => 'CM']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Cancelación de ventas', 'clave' => 'CV']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Cancelación de compra de mercancías a proveedor', 'clave' => 'CC']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Devolución a proveedores', 'clave' => 'DP']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Salida de mercancía caducada', 'clave' => 'SC']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Salida de mercancía especial', 'clave' => 'SM']);
        DB::table('tipo_movimientos')->insert(['tipo' => 'Venta de mercancías', 'clave' => 'VM']);
    }
}