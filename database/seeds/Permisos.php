<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Permisos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /* -------------------------------------------------------------------------- */
        /*                   creamos los permisos para cada modulos                   */
        /* -------------------------------------------------------------------------- */
        $permisos = [
            /**permisos de empresa */
            [
                'permiso' => 'actualizar información',
                'modulos_id' => 2
            ],
            [
                'permiso' => 'modificar firma electrónica',
                'modulos_id' => 2
            ],
            /**permisos de roles */
            [
                'permiso' => 'crear roles',
                'modulos_id' => 3
            ],
            [
                'permiso' => 'modificar permisos',
                'modulos_id' => 3
            ],
            [
                'permiso' => 'eliminar roles',
                'modulos_id' => 3
            ],
            [
                'permiso' => 'consultar permisos de un rol',
                'modulos_id' => 3
            ],
            /**permisos de usuarios/empleados */
            [
                'permiso' => 'crear usuarios',
                'modulos_id' => 4
            ],
            [
                'permiso' => 'modificar datos de usuario',
                'modulos_id' => 4
            ],
            [
                'permiso' => 'deshabilitar usuarios/empleados',
                'modulos_id' => 4
            ],
            [
                'permiso' => 'consultar información de usuario',
                'modulos_id' => 4
            ],
            /**permisos de clientes */
            [
                'permiso' => 'registrar clientes',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'modificar informacion',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'eliminar clientes',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'consultar información',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'consultar estados de cuenta',
                'modulos_id' => 5
            ],


            /**permisos de cementerio Financiamientos (se hara dedes la venta de cementerio)*/
            [
                'permiso' => 'Crear planes financiamientos de propiedades',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'Modificar financiamientos de propiedades',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'Deshabilitar financiamientos de propiedades',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'Consultar precios de propiedades',
                'modulos_id' => 7
            ],



            /**permisos de cementerio venta de terrenos */
            [
                'permiso' => 'registrar ventas',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'modificar contrato de ventas',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'cancelar contratos',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'consultar contratos',
                'modulos_id' => 7
            ],
            /**permisos de cementerio cobro de cuotas */
            [
                'permiso' => 'crear cobranzas anuales',
                'modulos_id' => 8
            ],
            [
                'permiso' => 'cancelar cobranza anual',
                'modulos_id' => 8
            ],
            [
                'permiso' => 'Actualizar lista de cobranza',
                'modulos_id' => 8
            ],
            /**permisos de invnetario proveedores */
            [
                'permiso' => 'agregar nuevos proveedores',
                'modulos_id' => 10
            ],
            [
                'permiso' => 'modificar datos de proveedor',
                'modulos_id' => 10
            ],
            [
                'permiso' => 'eliminar proveedores',
                'modulos_id' => 10
            ],
            [
                'permiso' => 'consultar datos de proveedor',
                'modulos_id' => 10
            ],
            /**permisos de invnetario articulos */
            [
                'permiso' => 'agregar articulos o servicios',
                'modulos_id' => 11
            ],
            [
                'permiso' => 'Modificar información',
                'modulos_id' => 11
            ],
            [
                'permiso' => 'Deshabilitar artículos o servicio',
                'modulos_id' => 11
            ],
            [
                'permiso' => 'Consultar información de un producto',
                'modulos_id' => 11
            ],
            /**permisos de ajuste de inventario */
            [
                'permiso' => 'Realizar Ajustes de inventario',
                'modulos_id' => 12
            ],
            [
                'permiso' => 'Consultar Ajustes de inventario',
                'modulos_id' => 12
            ],
            /**permisos de inventario crompras */
            [
                'permiso' => 'Capturar compra de mercancía',
                'modulos_id' => 13
            ],
            [
                'permiso' => 'Cancelar compras',
                'modulos_id' => 13
            ],
            [
                'permiso' => 'Consultar detalles de compra',
                'modulos_id' => 13
            ],
            /**permisos de inventario salidas */
            [
                'permiso' => 'Capturar salidas de mercancía del inventario',
                'modulos_id' => 14
            ],
            [
                'permiso' => 'Consultar detalles de salidas',
                'modulos_id' => 14
            ],

            /**permisos de funeraria Financiamientos*/
            [
                'permiso' => 'Crear planes funerarios a futuro',
                'modulos_id' => 16
            ],
            [
                'permiso' => 'Modificar Planes funerarios',
                'modulos_id' => 16
            ],
            [
                'permiso' => 'Deshabilitar planes de venta',
                'modulos_id' => 16
            ],
            [
                'permiso' => 'Consultar precios de planes',
                'modulos_id' => 16
            ],

            /**permisos de funeraria planes a futuro*/
            [
                'permiso' => 'Registrar ventas de planes a futuro',
                'modulos_id' => 16
            ],
            [
                'permiso' => 'Modificar contratos',
                'modulos_id' => 16
            ],
            [
                'permiso' => 'Cancelar contratos',
                'modulos_id' => 16
            ],
            [
                'permiso' => 'Consultar contratos',
                'modulos_id' => 16
            ],
            /**permisos de funeraria servicios funerarios*/
            [
                'permiso' => 'Atender servicios funerarios',
                'modulos_id' => 17
            ],
            [
                'permiso' => 'Modificar contratos de servicios',
                'modulos_id' => 17
            ],
            [
                'permiso' => 'Cancelar servicios funerarios',
                'modulos_id' => 17
            ],
            [
                'permiso' => 'Consultar contratos',
                'modulos_id' => 17
            ],
            /**permisos de funeraria ventas en gral*/
            [
                'permiso' => 'Realizar Ventas',
                'modulos_id' => 18
            ],
            [
                'permiso' => 'Cancelar Ventas',
                'modulos_id' => 18
            ],
            [
                'permiso' => 'Consultar venta',
                'modulos_id' => 18
            ],
            /**permisos de  pagos (cobranza)*/
            [
                'permiso' => 'Registrar pagos',
                'modulos_id' => 20
            ],
            [
                'permiso' => 'Cancelar pagos',
                'modulos_id' => 20
            ],
            [
                'permiso' => 'Consultar pagos',
                'modulos_id' => 20
            ],
            /**permisos de  facturas (cobranza)*/
            [
                'permiso' => 'Emitir Facturas',
                'modulos_id' => 21
            ],
            [
                'permiso' => 'Cancelar Facturas',
                'modulos_id' => 21
            ],
            [
                'permiso' => 'Consultar Facturas',
                'modulos_id' => 21
            ],

            /**permisos de reportes*/
            [
                'permiso' => 'Ver reporte de usuarios',
                'modulos_id' => 22
            ],
            [
                'permiso' => 'Ver reporte de clientes',
                'modulos_id' => 22
            ],
        ];

        /**CAPTURANDO LOS PERMISOS EN LA BASE DE DATOS */
        foreach ($permisos as $permiso) {
            DB::table('permisos')->insert([
                'permiso' => $permiso['permiso'],
                'modulos_id' =>  $permiso['modulos_id']
            ]);
        }
    }
}