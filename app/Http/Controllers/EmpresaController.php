<?php

namespace App\Http\Controllers;

use App\Cementerios;
use App\Crematorios;
use App\Facturacion;
use App\Funeraria;
use App\RegistroPublico;
use App\Salas;
use App\SATRegimenes;
use App\Velatorios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends ApiController
{

    public function get_datos_empresa()
    {
        return Funeraria::with('regimen')->with('registro_publico')->with('cementerio')->with(array('facturacion' => function ($query) {
            $query->select('id', 'funeraria_id', 'fecha_solicitud', 'fecha_vencimiento', 'cerfile', 'keyfile');
        }))->get();
    }

    public function get_regimenes()
    {
        return SATRegimenes::get();
    }

    /**UPDATE FUNERARIA */
    public function modificar_datos(Request $request)
    {

        //checando que tipo de modificacion es, es decir que parte de la info se va actualizar
        if ($request->modulo == "funeraria") {
            ///modificando funeraria
            request()->validate(
                [
                    'nombre_comercial' => 'required',
                    'razon_social'     => 'required',
                    'regimen.value'    => 'required',
                    'rfc'              => 'required',
                    'telefono'         => 'required',
                    'email'            => 'required|email',
                    'calle'            => 'required',
                    'num_ext'          => 'required',
                    'colonia'          => 'required',
                    'cp'               => 'required',
                    'zona.value'       => 'required',
                    'ciudad'           => 'required',
                    'estado'           => 'required',
                ],
                [
                    'required' => 'Dato requerido',
                    'email'    => 'ingrese un correo válido',
                ]
            );

            //se actualizan datos
            return DB::table('funeraria')->where('id', 1)->update(
                [
                    'nombre_comercial' => $request->nombre_comercial,
                    'razon_social'     => $request->razon_social,
                    'sat_regimenes_id' => $request->regimen['value'],
                    'rfc'              => $request->rfc,
                    'calle'            => $request->calle,
                    'num_ext'          => $request->num_ext,
                    'num_int'          => $request->num_int,
                    'colonia'          => $request->colonia,
                    'cp'               => $request->cp,
                    'ciudad'           => $request->ciudad,
                    'estado'           => $request->estado,
                    'zona_horaria'     => $request->zona['value'],
                    'telefono'         => $request->telefono,
                    'fax'              => $request->fax,
                    'email'            => $request->email,
                    'telefono'         => $request->telefono,
                    'cuenta'           => $request->cuenta,
                    'clabe'            => $request->clabe,
                ]
            );
        } else if ($request->modulo == "registro_publico") {
            ///modificando registro publico
            request()->validate(
                [
                    'rep_legal'  => 'required',
                    't_nep'      => 'required',
                    'fecha_tnep' => 'required',
                    'fe_lic'     => 'required',
                    'num_np'     => 'required',
                    'ciudad_np'  => 'required',
                    'estado_np'  => 'required',
                    'num_rpc'    => 'required',
                    'fecha_rpc'  => 'required',
                    'ciudad_rpc' => 'required',
                    'estado_rpc' => 'required',
                ],
                [
                    'required' => 'Dato requerido',

                ]
            );

            //return date('Y-m-d', strtotime($request->fecha_tnep));

            //se actualizan datos
            return DB::table('registro_publico')->where('id', 1)->update(
                [
                    'rep_legal'  => $request->rep_legal,
                    't_nep'      => $request->t_nep,
                    'fecha_tnep' => date('Y-m-d', strtotime($request->fecha_tnep)),
                    'fe_lic'     => $request->fe_lic,
                    'num_np'     => $request->num_np,
                    'ciudad_np'  => $request->ciudad_np,
                    'estado_np'  => $request->estado_np,
                    'num_rpc'    => $request->num_rpc,
                    'fecha_rpc'  => date('Y-m-d', strtotime($request->fecha_rpc)),
                    'ciudad_rpc' => $request->ciudad_rpc,
                    'estado_rpc' => $request->estado_rpc,
                ]
            );
        } else if ($request->modulo == "cementerio") {
            ///modificando cementerio
            request()->validate(
                [
                    'cementerio' => 'required',
                    'telefono'   => 'required',
                    'email'      => 'required|email',
                    'calle'      => 'required',
                    'num_ext'    => 'required',
                    'colonia'    => 'required',
                    'cp'         => 'required',
                    'ciudad'     => 'required',
                    'estado'     => 'required',
                ],
                [
                    'required' => 'Dato requerido',
                    'email'    => 'ingrese un correo válido',
                ]
            );

            //se actualizan datos
            return DB::table('cementerios')->where('id', 1)->update(
                [
                    'cementerio' => $request->cementerio,
                    'calle'      => $request->calle,
                    'num_ext'    => $request->num_ext,
                    'num_int'    => $request->num_int,
                    'colonia'    => $request->colonia,
                    'cp'         => $request->cp,
                    'ciudad'     => $request->ciudad,
                    'estado'     => $request->estado,
                    'telefono'   => $request->telefono,
                    'fax'        => $request->fax,
                    'email'      => $request->email,
                    'telefono'   => $request->telefono,
                ]
            );
        } else if ($request->modulo == "facturacion") {
            ///modificando facturacion
            request()->validate(
                [
                    'password'        => 'required|same:passwordRepetir',
                    'passwordRepetir' => 'required|same:password',
                ],
                [
                    'required' => 'Dato requerido',
                    'same'     => 'Las contraseñas no coinciden',
                ]
            );

            //////datos del certificado

            $fecha_solicitud     = '';
            $fecha_vencimiento   = '';
            $certificateFileName = '';
            $keyFileName         = '';
            if ($request->certificateFile !== 'null') {
                $extension = $request->certificateFile->getClientOriginalExtension();
                if ($extension !== 'cer') {
                    return $this->errorResponse(['message' => 'El archivo no es un certificado'], 412);
                }

                $path = $request->certificateFile->path();
                $data = getCertificateData($path);

                $serialArray = str_split($data['serialNumberHex']);
                //To validate serial number
                $finalCert = "";
                for ($x = 0; $x < count($serialArray); $x++) {
                    $finalCert .= ($x % 2 != 0) ? $serialArray[$x] : "";
                }

                if (!$finalCert or strlen($finalCert) != 20) {
                    return $this->errorResponse(['message' => 'El numero de certificado no es valido'], 412);
                }

                //Validates if certificate is valid
                $validFrom = new \DateTime();
                $validFrom->setTimestamp($data['validFrom_time_t']);
                $validTo = new \DateTime();
                $validTo->setTimestamp($data['validTo_time_t']);
                $today = new \DateTime();
                if (!($today >= $validFrom) and ($today <= $validTo)) {
                    return $this->errorResponse(['message' => 'El certificado no es valido'], 412);
                }

                //hasta aqui ek certificado es valido y se puede proceder
                $fecha_solicitud   = date_format($validFrom, 'Y-m-d H:i:s');
                $fecha_vencimiento = date_format($validTo, 'Y-m-d H:i:s');

                //validando el .key

                $extension = $request->keyFile->getClientOriginalExtension();
                if ($extension !== 'key') {
                    return $this->errorResponse(['message' => 'El archivo no es un archivo key'], 412);
                }

                //borro los certificados anteriores
                Storage::deleteDirectory('cer_production');
                //fin de borrado de certificados anteriores
                //borro el key anterio
                Storage::deleteDirectory('key_production');
                //fin de borradoel key anterio

                $time_created        = date('YmdHis');
                $certificateFileName = 'certificate_' . $time_created . "." . $request->certificateFile->getClientOriginalExtension();
                $request->certificateFile->storeAs('cer_production', $certificateFileName);
                $keyFileName = 'key_' . $time_created . "." . $request->keyFile->getClientOriginalExtension();
                $request->keyFile->storeAs('key_production', $keyFileName);
                //fin de datos del certificado
            }

            //se actualizan datos

            $facturacion = Facturacion::get()->first();

            //con cambio de contraseñas
            return DB::table('facturacion')->where('id', 1)->update(
                [
                    'keyFile'           => $request->keyFile === 'null' ? $facturacion->keyFile : $keyFileName,
                    'cerFile'           => $request->certificateFile === 'null' ? $facturacion->cerFile : $certificateFileName,
                    'password'          => $request->password === 'nochanges' ? $facturacion->password : $request->password,
                    'fecha_solicitud'   => $fecha_solicitud === '' ? $facturacion->fecha_solicitud : $fecha_solicitud,
                    'fecha_vencimiento' => $fecha_vencimiento === '' ? $facturacion->fecha_vencimiento : $fecha_vencimiento,
                ]
            );
        }
    }

    /**de aqui para abajo son cambios que tenia andres */

    public function save(Request $request)
    {
        $funerariaData = (object) $request->all();

        $funeraria = Funeraria::get()->first();
        if (!$funeraria) {
            $funeraria = new Funeraria;
        }

        foreach ($funerariaData as $key => $value) {
            $funeraria->{$key} = !is_null($funerariaData->{$key}) ? $funerariaData->{$key} : $funeraria->{$key};
        }

        $funeraria->save();

        return response()->json(['message' => 'funeraria creada satisfactoriamente'], 200);
    }

    public function saveRegistroPublico(Request $request)
    {
        $registroData = (object) $request->all();

        $funeraria = Funeraria::get()->first();
        $registro  = RegistroPublico::get()->first();
        if (!$registro) {
            $registro = new RegistroPublico;
        }

        foreach ($registroData as $key => $value) {
            $registro->{$key} = !is_null($registroData->{$key}) ? $registroData->{$key} : $registro->{$key};
        }
        $registro->funeraria_id = $funeraria->id;
        $registro->save();

        return response()->json(['message' => 'registro publico creado satisfactoriamente'], 200);
    }

    public function getRegistroPublico()
    {
        $registro = RegistroPublico::with('localidadNP')
            ->with('localidadNP.municipio')
            ->with('localidadNP.municipio.estado')
            ->with('localidadRPC')
            ->with('localidadRPC.municipio')
            ->with('localidadRPC.municipio.estado')
            ->get()->first();

        if ($registro) {
            $data = ['registro' => $registro];
            return response()->json($data, 200);
        }

        return $this->errorResponse('Registro Publico not found', 404);
    }

    public function get()
    {
        $funeraria = Funeraria::with('regimen')->with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data      = array();

        if ($funeraria) {
            $data = ['funeraria' => $funeraria];
            return response()->json($data, 200);
        }

        return $this->errorResponse('Funeraria not found', 404);
    }

    ///cementerio
    public function saveCementerio(Request $request)
    {
        $data = $request->all();

        $cementerio = Cementerios::get()->first();
        if (!$cementerio) {
            $cementerio = new Cementerios;
        }

        foreach ($data as $key => $value) {
            $cementerio->{$key} = !is_null($data[$key]) ? $data[$key] : $cementerio->{$key};
        }
        $funeraria                = Funeraria::get()->first();
        $cementerio->funeraria_id = $funeraria->id;

        $cementerio->save();
        return response()->json(['message' => 'cementerio guardado satisfactoriamente'], 200);
    }

    public function getCementerio()
    {
        $cementerio = Cementerios::with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data       = array();

        if ($cementerio) {
            return response()->json($cementerio, 200);
        }

        return $this->errorResponse('Cementerio not found', 404);
    }

    ///crematorio
    public function saveCrematorio(Request $request)
    {
        $data = $request->all();

        $crematorio = Crematorios::get()->first();
        if (!$crematorio) {
            $crematorio = new Crematorios;
        }

        foreach ($data as $key => $value) {
            $crematorio->{$key} = !is_null($data[$key]) ? $data[$key] : $crematorio->{$key};
        }
        $funeraria                = Funeraria::get()->first();
        $crematorio->funeraria_id = $funeraria->id;

        $crematorio->save();
        return response()->json(['message' => 'crematorio guardado satisfactoriamente'], 200);
    }

    public function getCrematorio()
    {
        $crematorio = Crematorios::with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data       = array();

        if ($crematorio) {
            return response()->json($crematorio, 200);
        }

        return $this->errorResponse('Crematorio not found', 404);
    }

    ///velatorios
    public function saveVelatorio(Request $request)
    {
        $data = $request->all();

        $velatorio = Velatorios::get()->first();
        if (!$velatorio) {
            $velatorio = new Velatorios;
        }

        $salas         = $data['salas'];
        $dataVelatorio = $data['velatorio'];
        foreach ($dataVelatorio as $key => $value) {
            $velatorio->{$key} = !is_null($dataVelatorio[$key]) ? $dataVelatorio[$key] : $velatorio->{$key};
        }
        $funeraria               = Funeraria::get()->first();
        $velatorio->funeraria_id = $funeraria->id;
        $velatorio->save();

        //Sala 1
        $sala1 = Salas::find(1);
        if (!$sala1) {
            $sala1 = new Salas;
        }
        $sala1->sala          = $salas[0];
        $sala1->velatorios_id = $velatorio->id;
        //Sala 2
        $sala2 = Salas::find(2);
        if (!$sala2) {
            $sala2 = new Salas;
        }
        $sala2->sala          = $salas[1];
        $sala2->velatorios_id = $velatorio->id;
        //Sala 3
        $sala3 = Salas::find(3);
        if (!$sala3) {
            $sala3 = new Salas;
        }
        $sala3->sala          = $salas[2];
        $sala3->velatorios_id = $velatorio->id;

        $sala1->save();
        $sala2->save();
        $sala3->save();
        return response()->json(['message' => 'velatorio guardado satisfactoriamente'], 200);
    }

    public function getVelatorio()
    {
        $velatorio = Velatorios::with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data      = array();

        if ($velatorio) {
            $sala1 = Salas::find(1);
            $sala2 = Salas::find(2);
            $sala3 = Salas::find(3);
            $salas = [$sala1->sala, $sala2->sala, $sala3->sala];

            return response()->json(['velatorio' => $velatorio, 'salas' => $salas], 200);
        }

        return $this->errorResponse('Velatorio not found', 404);
    }

    public function validateCERFile(Request $request)
    {
        $extension = $request->certificate->getClientOriginalExtension();
        if ($extension !== 'cer') {
            return $this->errorResponse(['message' => 'El archivo no es un certificado'], 412);
        }

        $path = $request->certificate->path();
        $data = getCertificateData($path);

        $serialArray = str_split($data['serialNumberHex']);
        //To validate serial number
        $finalCert = "";
        for ($x = 0; $x < count($serialArray); $x++) {
            $finalCert .= ($x % 2 != 0) ? $serialArray[$x] : "";
        }

        if (!$finalCert or strlen($finalCert) != 20) {
            return $this->errorResponse(['message' => 'El numero de certificado no es valido'], 412);
        }

        //Validates if certificate is valid
        $validFrom = new \DateTime();
        $validFrom->setTimestamp($data['validFrom_time_t']);
        $validTo = new \DateTime();
        $validTo->setTimestamp($data['validTo_time_t']);
        $today = new \DateTime();
        if (!($today >= $validFrom) and ($today <= $validTo)) {
            return $this->errorResponse(['message' => 'El certificado no es valido'], 412);
        }

        //hasta aqui ek certificado es valido y se puede proceder
        //se retornan las fechas para mostrar en el frontend
        $fecha_validez = [
            'fecha_validez' => fecha_no_day(date_format($validTo, 'Y-m-d H:i:s')),
        ];

        return $this->successResponse($fecha_validez, 200);
    }

    public function validateKEYFile(Request $request)
    {
        $extension = $request->key->getClientOriginalExtension();
        if ($extension !== 'key') {
            return $this->errorResponse(['message' => 'El archivo no es un archivo key'], 412);
        }

        return response()->json(['key' => ''], 200);
    }

    public function saveFacturacion(Request $request)
    {

        $data        = $request->all();
        $facturacion = Facturacion::get()->first();
        if (!$facturacion) {
            $facturacion = new Facturacion;
        }

        $request->password        = $request->password === 'null' ? null : $request->password;
        $request->certificateFile = $request->certificateFile === 'null' ? null : $request->certificateFile;
        $request->keyFile         = $request->keyFile === 'null' ? null : $request->keyFile;
        $request->email_emisor    = $request->email_emisor === 'null' ? null : $request->email_emisor;
        $request->sat_monedas_id  = $request->sat_monedas_id === 'null' ? null : $request->sat_monedas_id;

        if (!is_null($request->certificateFile)) {
            $certificateFileName = 'certificate_' . date('YmdHis') . "." . $request->certificateFile->getClientOriginalExtension();
            $request->certificateFile->storeAs('cer_production', $certificateFileName);
            $facturacion->cer = $certificateFileName;

            $path = $request->certificateFile->path();
            $data = getCertificateData($path);

            $serialArray = str_split($data['serialNumberHex']);
            //To validate serial number
            $finalCert = "";
            for ($x = 0; $x < count($serialArray); $x++) {
                $finalCert .= ($x % 2 != 0) ? $serialArray[$x] : "";
            }

            $facturacion->numero_cert = $finalCert;
        }

        if (!is_null($request->keyFile)) {
            $keyFileName = 'key_' . date('YmdHis') . "." . $request->keyFile->getClientOriginalExtension();
            $request->keyFile->storeAs('key_production', $keyFileName);
            $facturacion->key = $keyFileName;
        }

        $facturacion->password       = !is_null($request->password) ? $request->password : $facturacion->password;
        $facturacion->email_emisor   = !is_null($request->email_emisor) ? $request->email_emisor : $facturacion->email_emisor;
        $facturacion->sat_monedas_id = !is_null($request->sat_monedas_id) ? $request->sat_monedas_id : $facturacion->sat_monedas_id;

        $facturacion->save();

        return response()->json(['key' => ''], 200);
    }

    /**obtengo los datos de la empresa para crear los header de los reportes */
    public function get_empresa_data()
    {
        $funeraria = Funeraria::with('regimen')->get()->first();
        return $funeraria;
    }
}
