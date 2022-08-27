<?php

namespace App\Http\Controllers;

use App\Firmas;
use App\Documentos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;

class FirmasController  extends ApiController
{
    public function get_areas_firmar(Request $request, $id_documento = '')
    {
        $resultado = Documentos::with('areas.firma')->where('documentos.id', '=', $id_documento)->get()->toArray();
        if (count($resultado) > 0) {
            return $resultado;
        } else {
            return $this->errorResponse('No se encontró este documento', 409);
        }
    }

    public function get_firma($operacion_id = '', $area_id = '', $tipo = 'operacion')
    {
        $resultado = Firmas::where('areas_firmas_id', '=', $area_id)->where(function ($query) use ($operacion_id, $tipo) {
            if ($tipo == 'operacion') {
                $query->where('operacion_id', '=', $operacion_id);
            } else   if ($tipo == 'solicitud') {
                $query->where('solicitudes_id', '=', $operacion_id);
            }
        })
            ->get()->toArray();
        if (count($resultado) > 0) {
            /**se regresa el path de la imagen */
            if (Storage::disk('signatures')->exists($resultado[0]['id'] . '.png')) {
                $path = Storage::disk('signatures')->get($resultado[0]['id'] . '.png');
            } else {
                $path = Storage::disk('signatures')->get('default.png');
            }
            $resultado[0]['firma_path'] = 'data:image/png;base64,' . base64_encode($path);
            $resultado[0]['fecha_hora_firma'] = fecha_abr($resultado[0]['fecha_hora_firma']);
            return $resultado;
        } else {
            return 1;
        }
    }



    /**obtiene la imagen para plasmar en documentos */
    public function get_firma_documento($parametro_id = '', $area_id = '', $firma = "", $tipo = "operacion")
    {
        $array = [];
        if ($firma == "por_area_firma") {
            $resultado = Firmas::where('areas_firmas_id', '=', $area_id)->where(function ($query) use ($parametro_id, $tipo) {
                /**hasta la fecha control solo operaciones y solicitudes */
                if ($tipo == "operacion") {
                    $query->where('operacion_id', '=', $parametro_id);
                } else if ($tipo == "solicitud") {
                    $query->where('solicitudes_id', '=', $parametro_id);
                }
            })
                ->get()->toArray();
            if (count($resultado) > 0) {
                /**se regresa el path de la imagen */
                if (Storage::disk('signatures')->exists($resultado[0]['id'] . '.png')) {
                    $path = Storage::disk('signatures')->get($resultado[0]['id'] . '.png');
                } else {
                    $path = Storage::disk('signatures')->get('default.png');
                }
                $array['fecha_hora_firma'] = fecha_abr($resultado[0]['fecha_hora_firma']);
            } else {
                $array['fecha_hora_firma'] = "Pendiente";
                $path = Storage::disk('signatures')->get('default.png');
            }
        } else if ($firma == "por_vendedor") {
            if (Storage::disk('signatures')->exists('users/' . $parametro_id . '.png')) {
                $path = Storage::disk('signatures')->get('users/' . $parametro_id . '.png');
            } else {
                $path = Storage::disk('signatures')->get('default.png');
            }
        } else if ($firma == "por_gerente") {
            if (Storage::disk('signatures')->exists('users/2.png')) {
                $path = Storage::disk('signatures')->get('users/2.png');
            } else {
                $path = Storage::disk('signatures')->get('default.png');
            }
        } else if ($firma == "por_cobrador") {
            if (Storage::disk('signatures')->exists('users/' . $parametro_id . '.png')) {
                $path = Storage::disk('signatures')->get('users/' . $parametro_id . '.png');
            } else {
                $path = Storage::disk('signatures')->get('default.png');
            }
        }

        $array['firma_path'] = 'data:image/png;base64,' . base64_encode($path);
        return $array;
    }



    public function firmar(Request $request)
    {

        if (!isset($request->id_area)) {
            return $this->errorResponse('Ingrese la persona que está firmando.', 409);
        }
        if (!isset($request->operacion_id)) {
            return $this->errorResponse('Ingrese el documento que se está firmando.', 409);
        }
        if (!isset($request->firma)) {
            return $this->errorResponse('Capture la firma a digitalizar.', 409);
        }
        if (!isset($request->tipo)) {
            return $this->errorResponse('Capture el tipo de documento que se está firmando.', 409);
        }


        $operacion_id = $request->tipo == 'operacion' ? $request->operacion_id : null;
        $pago_id = $request->tipo == 'pagos' ? $request->operacion_id : null;
        $factura_id = $request->tipo == 'facturas' ? $request->operacion_id : null;
        $solicitudes_id = $request->tipo == 'solicitud' ? $request->operacion_id : null;



        if ($request->tipo == 'operacion') {
            /**checar si este documento ya fue firmado */
            $firma = DB::table('firmas')->where('areas_firmas_id', $request->id_area)->where('operacion_id', $operacion_id)->get();
        } else  if ($request->tipo == 'pagos') {
            $firma = DB::table('firmas')->where('areas_firmas_id', $request->id_area)->where('pagos_id', $pago_id)->get();
        }
        if ($request->tipo == 'facturas') {
            $firma = DB::table('firmas')->where('areas_firmas_id', $request->id_area)->where('facturas_id', $factura_id)->get();
        } else  if ($request->tipo == 'solicitud') {
            $firma = DB::table('firmas')->where('areas_firmas_id', $request->id_area)->where('solicitudes_id', $solicitudes_id)->get();
        }

        if (count($firma) > 0) {
            return $this->errorResponse('Está firma ya fue capturada anteriormente.', 409);
        }


        try {

            DB::beginTransaction();
            $id_firma = DB::table('firmas')->insertGetId(
                [
                    'firma_path'                    => null,
                    'registro_id'                   => (int) $request->user()->id,
                    'fecha_hora_firma'              => now(),
                    'solicitudes_id'                => $solicitudes_id,
                    'operacion_id'                  => $operacion_id,
                    'pagos_id'                      => $pago_id,
                    'facturas_id'                   => $factura_id,
                    'areas_firmas_id'               => $request->id_area,
                ]
            );


            if (trim($request->firma)) {
                $base64_image = $request->firma;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                    $data = substr($base64_image, strpos($base64_image, ',') + 1);
                    $data = base64_decode($data);
                    /**guardo la imagen aqui voy */
                    Storage::disk('signatures')->put($id_firma . '.png', $data);
                }
            }


            if (!Storage::disk('signatures')->exists($id_firma . '.png')) {
                return $this->errorResponse('Error al guardar la firma, por favor reintente.', 409);
            }

            DB::commit();
            return $id_firma;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}
