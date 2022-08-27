<?php

namespace App\Http\Controllers;

use App\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedoresController extends ApiController
{
    public function get_proveedores(Request $request, $id_proveedor = 'all', $paginated = '')
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $nombre_comercial = $request->nombre_comercial;
        $numero_control = $request->numero_control;
        $status = $request->status;
        $resultado_query = Proveedores::select(
            'id',
            'nombre_comercial',
            'razon_social',
            'nombre_contacto',
            'telefono',
            'nota',
            'direccion',
            'email',
            'status',
            DB::Raw('IF(proveedores.status=1 , "Activo","Inactivo" ) as status_texto')
        )
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        $q->where('proveedores.id', '=',  $numero_control);
                    } else if ($filtro_especifico_opcion == 2) {
                        $q->where('proveedores.telefono', '=',  $numero_control);
                    }
                }
            })
            ->where(function ($q) use ($nombre_comercial) {
                if (trim($nombre_comercial) != '') {
                    $q->where('proveedores.nombre_comercial', 'like', '%' . $nombre_comercial . '%');
                }
            })
            ->where(function ($q) use ($id_proveedor) {
                if (trim($id_proveedor) != '' && trim($id_proveedor != 'all')) {
                    $q->where('proveedores.id', '=', $id_proveedor);
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('proveedores.status', '=', $status);
                }
            })
            /**descartando el nombre_comercial publico en general */
            ->orderBy('proveedores.id', 'desc')
            ->get();
        $resultado = $resultado_query;
        if ($paginated == 'paginated') {
            $resultado = $this->showAllPaginated($resultado_query);
        }
        //se retorna el resultado
        return $resultado;
    }


    public function guardar_proveedor(Request $request)
    {
        $validaciones = [
            /**personal */
            'nombre_comercial' => 'required',
            'nombre_contacto' => 'required',
            'email' => 'email'
        ];

        $mensajes = [
            'required' => 'Este dato es obligatorio',
            'email.email' => 'Ingrese un email válido'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        return DB::table('proveedores')->insertGetId(
            [
                /**informacion fiscal */
                'nombre_comercial' => $request->nombre_comercial,
                'direccion' => $request->direccion,
                'razon_social' => $request->razon_social,
                'nombre_contacto' => $request->nombre_contacto,
                'telefono' => trim($request->telefono) != '' ? trim($request->telefono) : NULL,
                'email' => trim($request->email) != '' ? trim($request->email) : NULL,
                'nota' => trim($request->nota) != '' ? trim($request->nota) : NULL
            ]
        );
    }

    /**modificar proveedores */
    public function modificar_proveedor(Request $request)
    {
        $validaciones = [
            'nombre_comercial' => 'required',
            'nombre_contacto' => 'required',
            'email' => 'email',
            'id_proveedor_modificar' => 'required'
        ];
        $mensajes = [
            'required' => 'Este dato es obligatorio',
            'id_proveedor_modificar.required' => 'Ingrese el id del proveedor a modificar',
            'email.email' => 'Ingrese un email válido'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );
        $res = DB::table('proveedores')->where('id', $request->id_proveedor_modificar)->update(
            [
                /**informacion fiscal */
                'nombre_comercial' => $request->nombre_comercial,
                'direccion' => $request->direccion,
                'razon_social' => $request->razon_social,
                'nombre_contacto' => $request->nombre_contacto,
                'telefono' => trim($request->telefono) != '' ? trim($request->telefono) : NULL,
                'email' => trim($request->email) != '' ? trim($request->email) : NULL,
                'nota' => trim($request->nota) != '' ? trim($request->nota) : NULL
            ]
        );
        if ($res > 0) {
            return  $request->id_proveedor_modificar;
        } else {
            return 0;
        }
    }

    public function delete_proveedor(Request $request)
    {
        $proveedor_id = $request->proveedor_id;
        request()->validate(
            [
                'proveedor_id' => 'required',
            ],
            [
                'proveedor_id.required' => 'El ID del proveedor es necesario.',
            ]
        );
        return DB::table('proveedores')->where('id', $proveedor_id)->update(
            [
                'status' => 0,
            ]
        );
    }

    public function alta_proveedor(Request $request)
    {
        $proveedor_id = $request->proveedor_id;
        request()->validate(
            [
                'proveedor_id' => 'required',
            ],
            [
                'proveedor_id.required' => 'El ID del proveedor es necesario.',
            ]
        );
        return DB::table('proveedores')->where('id', $proveedor_id)->update(
            [
                'status' => 1,
            ]
        );
    }
}
